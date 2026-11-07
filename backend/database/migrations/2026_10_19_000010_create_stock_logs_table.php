<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // riwayat perubahan stok: tambah, kurangi, pesanan masuk, pesanan dibatalkan
        Schema::create('stock_logs', function (Blueprint $table) {
            $table->id('stock_log_id');
            $table->foreignId('product_id');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('change');                 // +10 atau -3
            $table->integer('stock_after');
            $table->string('reason', 150);
            $table->timestamps();

            $table->foreign('product_id')->references('product_id')->on('products')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_logs');
    }
};
