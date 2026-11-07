<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Nilai;
use Illuminate\Http\Request;

class NilaiController extends Controller
{
    public function index()
    {
        $nilais = Nilai::with(['mahasiswa', 'mataKuliah'])->get();

        return response()->json([
            'status' => 'success',
            'message' => 'List data nilai mahasiswa',
            'data' => $nilais
        ], 200);
    }

    // Method baru untuk menyimpan data POST
    public function store(Request $request)
    {
        $request->validate([
            'mahasiswa_id' => 'required|exists:mahasiswas,id',
            'mata_kuliah_id' => 'required|exists:mata_kuliahs,id',
            'semester' => 'required|string',
            'nilai' => 'required|numeric'
        ]);

        $nilai = Nilai::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Data nilai berhasil ditambahkan',
            'data' => $nilai
        ], 201);
    }
}