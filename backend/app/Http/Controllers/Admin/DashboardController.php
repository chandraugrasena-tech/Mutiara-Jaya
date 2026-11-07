<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // GET /admin/dashboard
    public function index()
    {
        $title = 'Dashboard';

        // pesanan dibatalkan tidak dihitung; pemasukan = pesanan yang sudah lunas
        $valid = Order::where('status', '!=', 'dibatalkan');
        $limit = (int) Setting::val('low_stock_limit');

        $stats = [
            ['label' => 'Jumlah Pesanan', 'value' => Order::count()],
            ['label' => 'Total Pemasukan (lunas)', 'value' => rp((clone $valid)->where('payment_status', 'lunas')->sum('total'))],
            ['label' => 'Pemasukan Hari Ini', 'value' => rp((clone $valid)->where('payment_status', 'lunas')->whereDate('created_at', today())->sum('total'))],
            ['label' => 'Pesanan Aktif', 'value' => Order::whereIn('status', ['diterima', 'dimasak', 'diantar'])->count()],
            ['label' => 'Jumlah Menu', 'value' => Product::count()],
            ['label' => 'Stok Menipis', 'value' => Product::where('stock', '<=', $limit)->count()],
        ];

        $statusCounts = Order::select('status', DB::raw('COUNT(*) as total'))->groupBy('status')->pluck('total', 'status');

        $topProducts = OrderItem::select('product_name', DB::raw('SUM(quantity) as sold'))
            ->whereHas('order', fn ($q) => $q->where('status', '!=', 'dibatalkan'))
            ->groupBy('product_name')
            ->orderByDesc('sold')
            ->limit(5)
            ->get();

        // pemasukan lunas 7 hari terakhir
        $daily = collect(range(6, 0))->map(function ($i) {
            $date = now()->subDays($i)->startOfDay();
            return [
                'label' => $date->format('d/m'),
                'total' => (int) Order::where('status', '!=', 'dibatalkan')
                    ->where('payment_status', 'lunas')
                    ->whereBetween('created_at', [$date, $date->copy()->endOfDay()])
                    ->sum('total'),
            ];
        });

        $lowStock = Product::where('stock', '<=', $limit)->orderBy('stock')->limit(5)->get();
        $recentOrders = Order::with(['items', 'user'])->latest('order_id')->limit(8)->get();

        return view('admin.dashboard', compact('title', 'stats', 'statusCounts', 'topProducts', 'daily', 'lowStock', 'limit', 'recentOrders'));
    }
}
