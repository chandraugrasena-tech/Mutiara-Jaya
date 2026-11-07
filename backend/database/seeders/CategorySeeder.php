<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Makanan Utama', 'Minuman', 'Camilan & Pelengkap'] as $name) {
            Category::create(['category' => $name]);
        }
    }
}
