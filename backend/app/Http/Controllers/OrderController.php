<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use App\Support\Cart;
use Illuminate\Http\Request;

// Checkout & riwayat pesanan pelanggan
class OrderController extends Controller
{
    // GET /orders
    public function index(Request $request)
    {
        $title = 'Pesanan saya';
        $orders = $request->user()->orders()->with('items')->latest('order_id')->paginate(10);

        return view('frontpage.orders', compact('title', 'orders'));
    }

    // POST /orders  (tombol "Pesan sekarang" di keranjang)
    public function store(Request $request, Cart $cart, OrderService $service)
    {
        $data = $request->validate([
            'payment_method' => 'required|in:CASH,QRIS',
            'notes' => 'nullable|string|max:255',
        ]);

        $user = $request->user();
        $address = $user->defaultAddress();

        if (! $address) {
            return redirect()->route('addresses.index')
                ->with('error', 'Tambahkan alamat pengantaran dulu.');
        }

        $summary = $cart->summary();

        $order = $service->create(
            $user,
            $summary['lines'],
            ['label' => $address->label, 'detail' => $address->detail, 'latitude' => $address->latitude, 'longitude' => $address->longitude],
            $data['payment_method'],
            $summary['selected_promo'],
            $data['notes'] ?? null
        );

        $cart->clear();

        return redirect()->route('orders.show', $order)
            ->with('success', 'Pesanan berhasil dibuat!');
    }

    // GET /orders/{order}
    public function show(Request $request, Order $order)
    {
        $this->authorizeOwner($request, $order);

        $order->load('items');
        $title = 'Pesanan #'.$order->order_code;

        return view('frontpage.order', compact('title', 'order'));
    }

    // POST /orders/{order}/pay   ("Saya sudah bayar" pada QRIS)
    public function pay(Request $request, Order $order)
    {
        $this->authorizeOwner($request, $order);

        if ($order->payment_method === 'QRIS' && $order->status !== 'dibatalkan') {
            $order->update(['payment_status' => 'lunas']);
        }

        return back()->with('success', 'Terima kasih, pembayaran QRIS dicatat.');
    }

    private function authorizeOwner(Request $request, Order $order): void
    {
        abort_unless($order->user_id === $request->user()->id, 403);
    }
}
