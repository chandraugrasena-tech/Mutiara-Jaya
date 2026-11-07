<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id');
            $table->string('order_code', 20)->unique();     // contoh: MJ2610191234
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            // alamat disalin (snapshot) supaya pesanan lama tidak berubah kalau alamat diedit
            $table->string('address_label', 30)->nullable();
            $table->text('address_detail')->nullable();
            $table->decimal('address_lat', 10, 7)->nullable();
            $table->decimal('address_lng', 10, 7)->nullable();
            $table->enum('payment_method', ['CASH', 'QRIS'])->default('CASH');
            $table->enum('payment_status', ['belum_bayar', 'lunas'])->default('belum_bayar');
            $table->unsignedInteger('subtotal');
            $table->unsignedInteger('ongkir')->default(0);
            $table->unsignedInteger('discount')->default(0);
            $table->unsignedInteger('total');
            $table->string('promo_title')->nullable();
            $table->string('notes')->nullable();
            $table->enum('status', ['diterima', 'dimasak', 'diantar', 'selesai', 'dibatalkan'])->default('diterima');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
