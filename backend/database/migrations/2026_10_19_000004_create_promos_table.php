<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promos', function (Blueprint $table) {
            $table->id('promo_id');
            $table->string('code', 30)->unique();
            $table->string('title', 150);
            $table->string('description')->nullable();
            $table->unsignedInteger('min_order')->default(0);
            $table->enum('type', ['ongkir', 'diskon', 'persen']);
            $table->unsignedInteger('value');
            $table->unsignedInteger('max_discount')->nullable();   // batas untuk tipe persen
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promos');
    }
};
