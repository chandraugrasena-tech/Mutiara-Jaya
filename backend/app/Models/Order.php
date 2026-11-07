<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $primaryKey = 'order_id';

    protected $fillable = [
        'order_code',
        'user_id',
        'address_label',
        'address_detail',
        'address_lat',
        'address_lng',
        'payment_method',
        'payment_status',
        'subtotal',
        'ongkir',
        'discount',
        'total',
        'promo_title',
        'notes',
        'status',
    ];

    protected $casts = [
        'subtotal' => 'integer',
        'ongkir' => 'integer',
        'discount' => 'integer',
        'total' => 'integer',
    ];

    // urutan status untuk tracking pesanan
    public const STATUS_STEPS = [
        'diterima' => ['title' => 'Pesanan diterima', 'desc' => 'Mutiara Jaya sudah menerima pesananmu'],
        'dimasak' => ['title' => 'Sedang dimasak', 'desc' => 'Dapur menyiapkan makananmu'],
        'diantar' => ['title' => 'Driver menuju lokasimu', 'desc' => 'Pesanan sedang diantar'],
        'selesai' => ['title' => 'Pesanan tiba', 'desc' => 'Selamat menikmati!'],
    ];

    public const STATUS_LABELS = [
        'diterima' => 'Diterima',
        'dimasak' => 'Dimasak',
        'diantar' => 'Diantar',
        'selesai' => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
    ];

    // url /order/{order_code} dan /admin/orders/{order_code} memakai kode pesanan
    public function getRouteKeyName(): string
    {
        return 'order_code';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function getTotalQtyAttribute(): int
    {
        return (int) $this->items->sum('quantity');
    }
}
