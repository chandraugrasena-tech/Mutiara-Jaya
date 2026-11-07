<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');
            $table->string('name', 150);
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->foreignId('category_id');
            $table->decimal('price', 12, 2);
            // path file di storage (products/xxx.jpg) ATAU url gambar eksternal (https://...)
            $table->string('img', 600)->nullable();
            $table->string('time', 30)->nullable();      // estimasi, contoh: 10-20 min
            $table->integer('stock')->default(100);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Foreign key ke categories
            $table->foreign('category_id')->references('category_id')->on('categories')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
