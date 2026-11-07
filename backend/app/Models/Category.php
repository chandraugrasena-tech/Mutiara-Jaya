<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    // primary key bukan 'id'
    protected $primaryKey = 'category_id';

    // field yang boleh diisi massal
    protected $fillable = ['category'];

    // satu kategori memiliki banyak produk (hasMany)
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}
