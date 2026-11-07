<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id('address_id');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('label', 30);        // Rumah / Kantor / Lainnya
            $table->text('detail');
            $table->boolean('is_default')->default(false);   // alamat "Anter ke"
            // koordinat dari tombol "Gunakan lokasi saya" (opsional)
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
