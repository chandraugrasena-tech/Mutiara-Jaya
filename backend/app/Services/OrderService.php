<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\Promo;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Logika bisnis pesanan. Dipakai bersama oleh halaman web (keranjang)
 * dan REST API (POST /api/orders), jadi aturan harga selalu sama.
 *
 * Satu "line" = ['product' => Product, 'quantity' => int]
 */
class OrderService
{
    // ongkos kirim diatur admin di menu Keterangan Toko
    public function deliveryFee(): int
    {
        return (int) Setting::val('delivery_fee');
    }

    /**
     * Hitung subtotal, ongkir, diskon, dan total (sama dengan CartContext.jsx versi React).
     */
    public function quote(iterable $lines, ?Promo $promo = null): array
    {
        $lines = collect($lines);

        $subtotal = (int) $lines->sum(fn ($l) => $l['product']->price * $l['quantity']);
        $ongkir = $subtotal > 0 ? $this->deliveryFee() : 0;

        // promo hanya berlaku kalau minimal belanja terpenuhi
        $active = ($promo && $promo->isEligible($subtotal)) ? $promo : null;
        $discount = $active ? $active->discountFor($subtotal, $ongkir) : 0;
        $total = $subtotal > 0 ? $subtotal + $ongkir - $discount : 0;

        return [
            'subtotal' => $subtotal,
            'ongkir' => $ongkir,
            'discount' => $discount,
            'total' => $total,
            'active_promo' => $active,
        ];
    }

    /**
     * Simpan pesanan + item-itemnya dalam satu transaksi database (DB::transaction),
     * lalu kurangi stok. Kalau ada yang gagal, semua perubahan dibatalkan.
     *
     * @param  array{label?:string,detail:string}  $address
     */
    public function create(
        User $user,
        iterable $lines,
        array $address,
        string $payment,
        ?Promo $promo = null,
        ?string $notes = null
    ): Order {
        $lines = collect($lines);

        if ($lines->isEmpty()) {
            throw ValidationException::withMessages(['cart' => 'Keranjang masih kosong.']);
        }
        if (empty(trim($address['detail'] ?? ''))) {
            throw ValidationException::withMessages(['address' => 'Alamat pengantaran belum dipilih.']);
        }

        return DB::transaction(function () use ($user, $lines, $address, $payment, $promo, $notes) {
            // baca ulang produk + kunci barisnya agar stok akurat
            $fresh = Product::whereIn('product_id', $lines->pluck('product.product_id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('product_id');

            $checked = collect();
            foreach ($lines as $line) {
                $product = $fresh->get($line['product']->product_id);
                $qty = (int) $line['quantity'];

                if (! $product || ! $product->is_active) {
                    throw ValidationException::withMessages([
                        'cart' => ($line['product']->name ?? 'Menu').' sudah tidak tersedia.',
                    ]);
                }
                if ($product->stock < $qty) {
                    throw ValidationException::withMessages([
                        'cart' => "Stok {$product->name} tinggal {$product->stock} porsi.",
                    ]);
                }
                $checked->push(['product' => $product, 'quantity' => $qty]);
            }

            $quote = $this->quote($checked, $promo);

            $order = Order::create([
                'order_code' => $this->generateCode(),
                'user_id' => $user->id,
                'address_label' => $address['label'] ?? null,
                'address_detail' => $address['detail'],
                'address_lat' => $address['latitude'] ?? null,
                'address_lng' => $address['longitude'] ?? null,
                'payment_method' => $payment,
                'payment_status' => 'belum_bayar',
                'subtotal' => $quote['subtotal'],
                'ongkir' => $quote['ongkir'],
                'discount' => $quote['discount'],
                'total' => $quote['total'],
                'promo_title' => $quote['active_promo']?->title,
                'notes' => $notes ?: null,
                'status' => 'diterima',
            ]);

            foreach ($checked as $line) {
                $p = $line['product'];
                $order->items()->create([
                    'product_id' => $p->product_id,
                    'product_name' => $p->name,
                    'product_img' => $p->img,
                    'price' => $p->price,
                    'quantity' => $line['quantity'],
                ]);
                $p->adjustStock(-$line['quantity'], 'Pesanan '.$order->order_code, $user->id);
            }

            return $order->load('items');
        });
    }

    private function generateCode(): string
    {
        do {
            $code = 'MJ'.now()->format('ymd').random_int(1000, 9999);
        } while (Order::where('order_code', $code)->exists());

        return $code;
    }
}
