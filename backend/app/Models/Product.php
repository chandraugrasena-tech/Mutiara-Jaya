<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class Product extends Model
{
    protected $primaryKey = 'product_id';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'category_id',
        'price',
        'img',
        'time',
        'stock',
        'is_active',
    ];

    protected $casts = [
        'price' => 'integer',
        'stock' => 'integer',
        'is_active' => 'boolean',
    ];

    // satu produk dimiliki satu kategori (belongsTo)
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // $product->image_url
    //  - url eksternal (https://...) dipakai apa adanya
    //  - file lokal 'assets/menu/xxx.jpg' dibuka dari folder public
    //  - file lama dari versi storage ('products/xxx.jpg') dibuka lewat /storage
    public function getImageUrlAttribute(): string
    {
        if (empty($this->img)) {
            return 'https://placehold.co/600x400/fff2f2/165823?text=Mutiara+Jaya';
        }
        if (Str::startsWith($this->img, ['http://', 'https://'])) {
            return $this->img;
        }
        if (Str::startsWith($this->img, 'products/')) {
            return url('storage/'.$this->img);
        }
        return asset($this->img);
    }

    // true kalau gambar tersimpan sebagai file lokal (bukan url eksternal)
    public function hasLocalImage(): bool
    {
        return ! empty($this->img) && ! Str::startsWith($this->img, ['http://', 'https://']);
    }

    // simpan upload ke public/assets/menu, hasilnya path: assets/menu/nama-menu-1700000000.jpg
    public static function storeImage(UploadedFile $file, string $name): string
    {
        $filename = Str::slug($name).'-'.time().'.'.$file->extension();

        return $file->storeAs('assets/menu', $filename, 'assets');
    }

    // hapus file gambar lokal dari disk yang sesuai
    public function deleteImageFile(): void
    {
        if (! $this->hasLocalImage()) {
            return;
        }
        $disk = Str::startsWith($this->img, 'products/') ? 'public' : 'assets';
        Storage::disk($disk)->delete($this->img);
    }

    // Ubah stok (+ menambah, - mengurangi) sekaligus mencatat riwayatnya di stock_logs.
    // Stok tidak boleh menjadi minus.
    public function adjustStock(int $delta, string $reason, ?int $userId = null): void
    {
        DB::transaction(function () use ($delta, $reason, $userId) {
            $fresh = static::whereKey($this->getKey())->lockForUpdate()->first();
            $new = $fresh->stock + $delta;

            if ($new < 0) {
                throw ValidationException::withMessages([
                    'stock' => "Stok {$fresh->name} tidak cukup untuk dikurangi (tersisa {$fresh->stock}).",
                ]);
            }

            $fresh->update(['stock' => $new]);

            StockLog::create([
                'product_id' => $fresh->getKey(),
                'user_id' => $userId,
                'change' => $delta,
                'stock_after' => $new,
                'reason' => $reason,
            ]);

            $this->stock = $new;
        });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        if ($keyword === null || trim($keyword) === '') {
            return $query;
        }
        return $query->where(function ($q) use ($keyword) {
            $q->where('name', 'like', "%{$keyword}%")
              ->orWhere('description', 'like', "%{$keyword}%");
        });
    }
}
