<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockLog extends Model
{
    protected $primaryKey = 'stock_log_id';

    protected $fillable = ['product_id', 'user_id', 'change', 'stock_after', 'reason'];

    protected $casts = [
        'change' => 'integer',
        'stock_after' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
