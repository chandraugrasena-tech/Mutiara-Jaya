<?php

namespace App\Support;

use App\Models\Product;
use App\Models\Promo;
use App\Services\OrderService;
use Illuminate\Support\Collection;

/**
 * Keranjang belanja berbasis SESSION (pengganti localStorage "mj_cart" di versi React).
 * Isi session: [product_id => quantity]
 */
class Cart
{
    private const KEY = 'mj_cart';
    private const PROMO_KEY = 'mj_promo';

    public function __construct(private OrderService $orders)
    {
    }

    private function raw(): array
    {
        return session(self::KEY, []);
    }

    public function add(int $productId, int $qty = 1): void
    {
        $cart = $this->raw();
        $cart[$productId] = ($cart[$productId] ?? 0) + max(1, $qty);
        session([self::KEY => $cart]);
    }

    public function change(int $productId, int $delta): void
    {
        $cart = $this->raw();
        if (! isset($cart[$productId])) {
            return;
        }
        $cart[$productId] += $delta;
        if ($cart[$productId] <= 0) {
            unset($cart[$productId]);
        }
        session([self::KEY => $cart]);
    }

    public function remove(int $productId): void
    {
        $cart = $this->raw();
        unset($cart[$productId]);
        session([self::KEY => $cart]);
    }

    public function clear(): void
    {
        session()->forget([self::KEY, self::PROMO_KEY]);
    }

    public function count(): int
    {
        return (int) array_sum($this->raw());
    }

    /** @return Collection<int, array{product: Product, quantity: int}> */
    public function lines(): Collection
    {
        $cart = $this->raw();
        if (empty($cart)) {
            return collect();
        }

        $products = Product::with('category')
            ->active()
            ->whereIn('product_id', array_keys($cart))
            ->get()
            ->keyBy('product_id');

        $lines = collect();
        foreach ($cart as $id => $qty) {
            if ($products->has($id)) {
                $lines->push(['product' => $products[$id], 'quantity' => (int) $qty]);
            }
        }
        return $lines;
    }

    public function selectPromo(?string $code): void
    {
        if ($code) {
            session([self::PROMO_KEY => strtoupper(trim($code))]);
        } else {
            session()->forget(self::PROMO_KEY);
        }
    }

    // promo yang dipilih (belum tentu memenuhi minimal belanja)
    public function selectedPromo(): ?Promo
    {
        $code = session(self::PROMO_KEY);
        return $code ? Promo::where('code', $code)->where('is_active', true)->first() : null;
    }

    public function summary(): array
    {
        $lines = $this->lines();
        $selected = $this->selectedPromo();
        $quote = $this->orders->quote($lines, $selected);

        return $quote + [
            'lines' => $lines,
            'selected_promo' => $selected,
        ];
    }
}
