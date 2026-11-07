<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Promo;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Database\Seeder;

// Contoh pesanan supaya Dashboard admin tidak kosong saat pertama dibuka
class OrderSeeder extends Seeder
{
    public function run(OrderService $service): void
    {
        $user = User::where('email', 'pelanggan@mutiarajaya.id')->first();
        $address = $user->defaultAddress();
        $addr = ['label' => $address->label, 'detail' => $address->detail];

        $samples = [
            // [[nama menu => qty], pembayaran, kode promo, status, umur (hari)]
            [['Ayam Geprek Sambal Matah' => 2, 'Es Jeruk Peras' => 2], 'CASH', 'ONGKIR10', 'selesai', 3],
            [['Nasi Campur Lengkap' => 1, 'Es Milo Cokelat' => 1, 'Kerupuk Kaleng Renyah' => 2], 'QRIS', 'DISKON5', 'selesai', 1],
            [['Sate Ayam Madura (10 Tusuk)' => 2, 'Nasi Goreng Spesial Telur' => 1], 'QRIS', 'HEMAT10', 'dimasak', 0],
        ];

        foreach ($samples as [$menu, $payment, $promoCode, $status, $daysAgo]) {
            $lines = [];
            foreach ($menu as $name => $qty) {
                $lines[] = ['product' => Product::where('name', $name)->first(), 'quantity' => $qty];
            }

            $order = $service->create($user, $lines, $addr, $payment, Promo::where('code', $promoCode)->first(), null);

            $order->forceFill([
                'status' => $status,
                'payment_status' => ($payment === 'QRIS' || $status === 'selesai') ? 'lunas' : 'belum_bayar',
                'created_at' => now()->subDays($daysAgo),
                'updated_at' => now()->subDays($daysAgo),
            ])->save();   // forceFill karena created_at tidak termasuk $fillable
        }
    }
}
