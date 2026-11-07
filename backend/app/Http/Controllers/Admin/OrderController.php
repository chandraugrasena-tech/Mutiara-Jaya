<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Pengelolaan pesanan masuk oleh admin
class OrderController extends Controller
{
    // GET /admin/orders?s=&status=
    public function index(Request $request)
    {
        $title = 'Pesanan Masuk';

        $orders = Order::with(['items', 'user'])
            ->when($request->filled('s'), function ($q) use ($request) {
                $s = $request->s;
                $q->where(function ($w) use ($s) {
                    $w->where('order_code', 'like', "%{$s}%")
                      ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$s}%"));
                });
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest('order_id')
            ->paginate(10)
            ->withQueryString();

        $statuses = Order::STATUS_LABELS;

        return view('admin.orders.index', compact('title', 'orders', 'statuses'));
    }

    // GET /admin/orders/{order}
    public function show(Order $order)
    {
        $order->load(['items', 'user']);
        $title = 'Pesanan #'.$order->order_code;
        $statuses = Order::STATUS_LABELS;

        return view('admin.orders.show', compact('title', 'order', 'statuses'));
    }

    // PATCH /admin/orders/{order}
    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => 'required|in:'.implode(',', array_keys(Order::STATUS_LABELS)),
            'payment_status' => 'nullable|in:belum_bayar,lunas',
        ]);

        if ($order->status === 'dibatalkan') {
            return back()->with('error', 'Pesanan yang sudah dibatalkan tidak dapat diubah.');
        }

        DB::transaction(function () use ($order, $data) {
            // pesanan dibatalkan -> stok dikembalikan
            if ($data['status'] === 'dibatalkan') {
                foreach ($order->items as $item) {
                    $product = $item->product_id ? Product::find($item->product_id) : null;
                    $product?->adjustStock($item->quantity, 'Pembatalan '.$order->order_code, auth()->id());
                }
            }

            $payment = $data['payment_status'] ?? $order->payment_status;
            // CASH dibayar di tempat: otomatis lunas saat pesanan selesai
            if ($data['status'] === 'selesai' && $order->payment_method === 'CASH') {
                $payment = 'lunas';
            }

            $order->update(['status' => $data['status'], 'payment_status' => $payment]);
        });

        return back()->with('success', 'Status pesanan diperbarui.');
    }
}
