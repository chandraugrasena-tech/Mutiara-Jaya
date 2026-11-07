<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PromoController extends Controller
{
    private array $messages = [
        'required' => 'Kolom :attribute harus diisi',
        'integer' => 'Kolom :attribute harus bilangan bulat',
        'unique' => 'Kode promo sudah dipakai',
    ];

    public function index(Request $request)
    {
        $title = 'Daftar Promo';
        $promos = Promo::query()
            ->when($request->filled('s'), fn ($q) => $q->where(function ($w) use ($request) {
                $w->where('code', 'like', '%'.$request->s.'%')->orWhere('title', 'like', '%'.$request->s.'%');
            }))
            ->orderBy('promo_id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.promos.index', compact('title', 'promos'));
    }

    public function create()
    {
        $title = 'Tambah Promo';

        return view('admin.promos.form', compact('title'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        Promo::create($data);

        return redirect()->route('admin.promos.index')->with('success', 'Promo ditambahkan.');
    }

    public function edit(Promo $promo)
    {
        $title = 'Edit Promo '.$promo->code;

        return view('admin.promos.form', compact('title', 'promo'));
    }

    public function update(Request $request, Promo $promo)
    {
        $data = $this->validated($request, $promo);
        $promo->update($data);

        return redirect()->route('admin.promos.index')->with('success', 'Promo diperbarui.');
    }

    public function destroy(Promo $promo)
    {
        $promo->delete();

        return redirect()->route('admin.promos.index')->with('success', 'Promo dihapus.');
    }

    private function validated(Request $request, ?Promo $promo = null): array
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:30', Rule::unique('promos', 'code')->ignore($promo?->promo_id, 'promo_id')],
            'title' => 'required|string|max:150',
            'description' => 'nullable|string|max:255',
            'type' => ['required', Rule::in(array_keys(Promo::TYPES))],
            'value' => 'required|integer|min:1',
            'min_order' => 'required|integer|min:0',
            'max_discount' => 'nullable|integer|min:0',
        ], $this->messages);

        $data['code'] = strtoupper(trim($data['code']));
        $data['is_active'] = $request->boolean('is_active');
        // batas diskon hanya relevan untuk tipe persen
        if ($data['type'] !== 'persen') {
            $data['max_discount'] = null;
        }

        return $data;
    }
}
