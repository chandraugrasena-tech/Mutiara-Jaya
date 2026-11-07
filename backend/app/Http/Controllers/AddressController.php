<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;

// Alamat "Anter ke" milik user yang login
class AddressController extends Controller
{
    private const LABELS = ['Rumah', 'Kantor', 'Lainnya'];

    // GET /addresses  (?edit=ID untuk mengubah)
    public function index(Request $request)
    {
        $title = 'Alamat pengantaran';
        $addresses = $request->user()->addresses()->orderBy('address_id')->get();
        $editing = $request->filled('edit') ? $addresses->firstWhere('address_id', (int) $request->edit) : null;
        $labels = self::LABELS;

        return view('frontpage.addresses', compact('title', 'addresses', 'editing', 'labels'));
    }

    // POST /addresses
    public function store(Request $request)
    {
        $data = $this->validated($request);
        $user = $request->user();

        // alamat pertama otomatis jadi pilihan
        $isFirst = ! $user->addresses()->exists();
        $address = $user->addresses()->create($data + ['is_default' => $isFirst]);

        // alamat baru langsung dipilih (seperti saveAddress() di React)
        $this->makeDefault($address);

        return redirect($this->backTo($request))->with('success', 'Alamat baru disimpan.');
    }

    // PUT /addresses/{address}
    public function update(Request $request, Address $address)
    {
        $this->authorizeOwner($address);
        $address->update($this->validated($request));
        $this->makeDefault($address);

        return redirect($this->backTo($request))->with('success', 'Alamat diperbarui.');
    }

    // POST /addresses/{address}/select
    public function select(Request $request, Address $address)
    {
        $this->authorizeOwner($address);
        $this->makeDefault($address);

        return redirect($this->backTo($request))->with('success', 'Alamat pengantaran dipilih.');
    }

    // DELETE /addresses/{address}
    public function destroy(Request $request, Address $address)
    {
        $this->authorizeOwner($address);

        if ($request->user()->addresses()->count() <= 1) {
            return back()->with('error', 'Minimal harus ada satu alamat.');
        }

        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault) {
            $next = $request->user()->addresses()->orderBy('address_id')->first();
            if ($next) {
                $this->makeDefault($next);
            }
        }

        return redirect()->route('addresses.index')->with('success', 'Alamat dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'label' => 'required|in:'.implode(',', self::LABELS),
            'detail' => 'required|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ], [
            'detail.required' => 'Alamat lengkap harus diisi',
        ]);
    }

    private function makeDefault(Address $address): void
    {
        // update lewat query langsung (bukan model) supaya tetap tersimpan
        // walaupun alamat ini sebelumnya sudah berstatus terpilih
        Address::where('user_id', $address->user_id)->update(['is_default' => false]);
        Address::where('address_id', $address->address_id)->update(['is_default' => true]);
        $address->is_default = true;
    }

    private function authorizeOwner(Address $address): void
    {
        abort_unless($address->user_id === auth()->id(), 403);
    }

    // setelah pilih alamat dari keranjang, kembali ke keranjang
    private function backTo(Request $request): string
    {
        return $request->input('from') === 'cart' ? route('cart.index') : route('addresses.index');
    }
}
