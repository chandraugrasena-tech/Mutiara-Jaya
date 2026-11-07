<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id('order_item_id');
            $table->foreignId('order_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('product_name', 150);
            $table->string('product_img', 600)->nullable();
            $table->unsignedInteger('price');
            $table->unsignedInteger('quantity');
            $table->timestamps();

            $table->foreign('order_id')->references('order_id')->on('orders')->onDelete('cascade');
            // kalau menu dihapus, riwayat pesanan tetap ada
            $table->foreign('product_id')->references('product_id')->on('products')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
