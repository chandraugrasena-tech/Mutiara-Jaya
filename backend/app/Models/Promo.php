<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    protected $primaryKey = 'promo_id';

    protected $fillable = [
        'code',
        'title',
        'description',
        'min_order',
        'type',
        'value',
        'max_discount',
        'is_active',
    ];

    protected $casts = [
        'min_order' => 'integer',
        'value' => 'integer',
        'max_discount' => 'integer',
        'is_active' => 'boolean',
    ];

    public const TYPES = [
        'ongkir' => 'Potongan ongkir',
        'diskon' => 'Diskon nominal',
        'persen' => 'Diskon persen',
    ];

    public function isEligible(int $subtotal): bool
    {
        return $this->is_active && $subtotal >= $this->min_order;
    }

    // Sama dengan getDiscount() pada promos.js versi React
    public function discountFor(int $subtotal, int $ongkir): int
    {
        if (! $this->isEligible($subtotal)) {
            return 0;
        }
        return match ($this->type) {
            'ongkir' => min($this->value, $ongkir),
            'diskon' => min($this->value, $subtotal),
            'persen' => min(
                (int) round($subtotal * $this->value / 100),
                $this->max_discount ?? PHP_INT_MAX
            ),
            default => 0,
        };
    }
}
