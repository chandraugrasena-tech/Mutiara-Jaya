<?php

namespace Database\Seeders;

use App\Models\Promo;
use Illuminate\Database\Seeder;

// Sama dengan PROMOS pada src/utils/promos.js versi React
class PromoSeeder extends Seeder
{
    public function run(): void
    {
        $promos = [
            [
                'code' => 'ONGKIR10',
                'title' => 'Gratis Ongkir Rp 10.000',
                'description' => 'Bebas ongkir ke semua area antar',
                'min_order' => 30000,
                'type' => 'ongkir',
                'value' => 10000,
                'max_discount' => null,
            ],
            [
                'code' => 'DISKON5',
                'title' => 'Diskon Makanan Rp 5.000',
                'description' => 'Potongan langsung untuk total makanan',
                'min_order' => 25000,
                'type' => 'diskon',
                'value' => 5000,
                'max_discount' => null,
            ],
            [
                'code' => 'HEMAT10',
                'title' => 'Diskon 10% (maks. Rp 8.000)',
                'description' => 'Hemat 10% dari total makanan',
                'min_order' => 40000,
                'type' => 'persen',
                'value' => 10,
                'max_discount' => 8000,
            ],
        ];

        foreach ($promos as $promo) {
            Promo::create($promo + ['is_active' => true]);
        }
    }
}
