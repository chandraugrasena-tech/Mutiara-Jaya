<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use App\Models\Promo;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // GET /api/orders  (pelanggan: pesanan miliknya, admin: semua pesanan)
    public function index(Request $request)
    {
        $user = $request->user();

        $orders = Order::with(['items', 'user'])
            ->when(! $user->isAdmin(), fn ($q) => $q->where('user_id', $user->id))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest('order_id')
            ->paginate(10)
            ->withQueryString();

        return OrderResource::collection($orders);
    }

    // POST /api/orders
    public function store(Request $request, OrderService $service)
    {
        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,product_id',
            'items.*.quantity' => 'required|integer|min:1|max:99',
            'address' => 'required|string|max:500',
            'address_label' => 'nullable|string|max:30',
            'payment_method' => 'required|in:CASH,QRIS',
            'promo_code' => 'nullable|string|exists:promos,code',
            'notes' => 'nullable|string|max:255',
        ]);

        $products = Product::whereIn('product_id', collect($data['items'])->pluck('product_id'))->get()->keyBy('product_id');

        $lines = collect($data['items'])->map(fn ($i) => [
            'product' => $products[$i['product_id']],
            'quantity' => (int) $i['quantity'],
        ]);

        $promo = ! empty($data['promo_code'])
            ? Promo::where('code', strtoupper($data['promo_code']))->where('is_active', true)->first()
            : null;

        $order = $service->create(
            $request->user(),
            $lines,
            ['label' => $data['address_label'] ?? 'Rumah', 'detail' => $data['address']],
            $data['payment_method'],
            $promo,
            $data['notes'] ?? null
        );

        return (new OrderResource($order))->response()->setStatusCode(201);
    }

    // GET /api/orders/{kode_pesanan}
    public function show(Request $request, Order $order)
    {
        abort_unless($request->user()->isAdmin() || $order->user_id === $request->user()->id, 403, 'Bukan pesanan kamu.');

        return new OrderResource($order->load(['items', 'user']));
    }
}
