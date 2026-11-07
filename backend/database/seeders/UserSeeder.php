<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Akun admin  -> login di /login lalu masuk ke /admin/dashboard
        User::create([
            'name' => 'Admin Mutiara Jaya',
            'email' => 'admin@mutiarajaya.id',
            'password' => 'admin123',
            'role' => 'admin',
            'phone' => '0895622759692',
        ]);

        // Akun pelanggan demo (alamat sama dengan DEFAULT_ADDRESSES versi React)
        $customer = User::create([
            'name' => 'Pelanggan Demo',
            'email' => 'pelanggan@mutiarajaya.id',
            'password' => 'password',
            'role' => 'customer',
            'phone' => '081234567890',
        ]);

        $customer->addresses()->createMany([
            ['label' => 'Rumah', 'detail' => 'Jl. Udayana No. 18, Singaraja', 'is_default' => true],
            ['label' => 'Kantor', 'detail' => 'Jl. Ahmad Yani No. 5, Singaraja', 'is_default' => false],
        ]);
    }
}
