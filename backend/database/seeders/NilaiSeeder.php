<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Nilai;

class NilaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat data dummy Mahasiswa
        $mhs = Mahasiswa::create([
            'nama' => 'Chandra'
        ]);

        // Membuat data dummy Mata Kuliah
        $mk = MataKuliah::create([
            'nama_mk' => 'Pemrograman Web Lanjut'
        ]);

        // Membuat data dummy Nilai yang menghubungkan Mahasiswa dan Mata Kuliah
        Nilai::create([
            'mahasiswa_id' => $mhs->id,
            'mata_kuliah_id' => $mk->id,
            'semester' => 'Gasal 2026',
            'nilai' => 88.50
        ]);
    }
}
