<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

// Keterangan toko: nama, alamat, jam buka, kontak, ongkir, batas stok menipis
class SettingController extends Controller
{
    public function edit()
    {
        $title = 'Keterangan Toko';
        $values = [];
        foreach (array_keys(Setting::DEFAULTS) as $key) {
            $values[$key] = Setting::val($key);
        }
        // alamat dan jam buka sengaja dibiarkan kosong sampai admin mengisinya
        $values['store_address'] = Setting::query()->where('key', 'store_address')->value('value') ?? '';
        $values['store_hours'] = Setting::query()->where('key', 'store_hours')->value('value') ?? '';

        return view('admin.settings.edit', compact('title', 'values'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'store_name' => 'required|string|max:100',
            'store_tagline' => 'nullable|string|max:150',
            'store_description' => 'nullable|string|max:1000',
            'store_address' => 'nullable|string|max:300',
            'store_hours' => 'nullable|string|max:150',
            'store_phone' => 'nullable|string|max:30',
            'store_whatsapp' => 'nullable|string|max:20',
            'store_email' => 'nullable|email|max:100',
            'delivery_area' => 'nullable|string|max:100',
            'delivery_fee' => 'required|integer|min:0|max:1000000',
            'low_stock_limit' => 'required|integer|min:0|max:100000',
        ], [
            'required' => 'Kolom :attribute harus diisi',
            'integer' => 'Kolom :attribute harus berupa angka bulat',
            'email' => 'Format email tidak valid',
        ]);

        foreach ($data as $key => $value) {
            Setting::put($key, $value === null ? null : (string) $value);
        }

        return redirect()->route('admin.settings.edit')->with('success', 'Keterangan toko disimpan.');
    }
}
