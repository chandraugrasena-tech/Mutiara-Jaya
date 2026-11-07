<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // php artisan migrate:fresh --seed
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            PromoSeeder::class,
            OrderSeeder::class,
            // data contoh API nilai mahasiswa milik proyek sebelumnya (tidak diubah)
            NilaiSeeder::class,
        ]);
    }
}
