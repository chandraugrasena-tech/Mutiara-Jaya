<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Keterangan toko yang bisa diubah admin lewat menu "Keterangan Toko"
class Setting extends Model
{
    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    // nilai awal dipakai kalau admin belum mengisi
    public const DEFAULTS = [
        'store_name' => 'Mutiara Jaya',
        'store_tagline' => 'Pesan Antar Cepat & Hangat',
        'store_description' => 'Mutiara Jaya adalah layanan pesan antar makanan dan minuman. Pilih menu favoritmu, tentukan lokasi pengantaran, lalu pantau pesananmu sampai tiba.',
        'store_address' => '',
        'store_hours' => '',
        'store_phone' => '0895-6227-69692',
        'store_whatsapp' => '0895622759692',
        'store_email' => 'admin@mutiarajaya.id',
        'delivery_area' => 'Singaraja',
        'delivery_fee' => '10000',
        'low_stock_limit' => '10',
    ];

    // Setting::val('store_name')
    public static function val(string $key): string
    {
        $value = static::query()->where('key', $key)->value('value');

        return ($value === null || $value === '') ? (self::DEFAULTS[$key] ?? '') : (string) $value;
    }

    public static function put(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
