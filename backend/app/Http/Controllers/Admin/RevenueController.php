<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

// Pemasukan: hanya pesanan yang sudah lunas dihitung sebagai pemasukan
class RevenueController extends Controller
{
    // GET /admin/pemasukan?from=2026-11-01&to=2026-11-30
    public function index(Request $request)
    {
        $title = 'Pemasukan';

        $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
        ]);

        $from = $request->filled('from') ? Carbon::parse($request->from)->startOfDay() : now()->startOfMonth();
        $to = $request->filled('to') ? Carbon::parse($request->to)->endOfDay() : now()->endOfDay();

        $orders = Order::where('status', '!=', 'dibatalkan')
            ->whereBetween('created_at', [$from, $to])
            ->get(['order_id', 'total', 'payment_method', 'payment_status', 'created_at']);

        $paid = $orders->where('payment_status', 'lunas');
        $unpaid = $orders->where('payment_status', 'belum_bayar');

        $summary = [
            'orders' => $orders->count(),
            'income' => (int) $paid->sum('total'),
            'pending' => (int) $unpaid->sum('total'),
            'average' => $paid->count() ? (int) round($paid->sum('total') / $paid->count()) : 0,
            'cash' => (int) $paid->where('payment_method', 'CASH')->sum('total'),
            'qris' => (int) $paid->where('payment_method', 'QRIS')->sum('total'),
        ];

        // rekap per hari
        $perDay = $orders->groupBy(fn ($o) => $o->created_at->format('Y-m-d'))
            ->map(fn ($group, $date) => [
                'date' => Carbon::parse($date),
                'orders' => $group->count(),
                'income' => (int) $group->where('payment_status', 'lunas')->sum('total'),
                'pending' => (int) $group->where('payment_status', 'belum_bayar')->sum('total'),
            ])
            ->sortKeysDesc()
            ->values();

        $topProducts = OrderItem::select('product_name', DB::raw('SUM(quantity) as qty'), DB::raw('SUM(price * quantity) as revenue'))
            ->whereHas('order', fn ($q) => $q->where('status', '!=', 'dibatalkan')->whereBetween('created_at', [$from, $to]))
            ->groupBy('product_name')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        return view('admin.revenue.index', compact('title', 'from', 'to', 'summary', 'perDay', 'topProducts'));
    }
}
