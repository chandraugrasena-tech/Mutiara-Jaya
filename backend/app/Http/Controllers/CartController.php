<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Promo;
use App\Support\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private Cart $cart)
    {
    }

    // GET /cart
    public function index()
    {
        $title = 'Keranjang';

        $summary = $this->cart->summary();
        $address = auth()->user()->defaultAddress();

        return view('frontpage.cart', compact('title', 'summary', 'address'));
    }

    // POST /cart/{product}
    public function add(Request $request, Product $product)
    {
        abort_unless($product->is_active, 404);

        $qty = (int) $request->input('qty', 1);
        $this->cart->add($product->product_id, min(max($qty, 1), 99));

        return back()->with('success', $product->name.' masuk keranjang.');
    }

    // PATCH /cart/{product}   (delta = +1 / -1)
    public function update(Request $request, Product $product)
    {
        $this->cart->change($product->product_id, (int) $request->input('delta', 0));

        return redirect()->route('cart.index');
    }

    // DELETE /cart/{product}
    public function destroy(Product $product)
    {
        $this->cart->remove($product->product_id);

        return redirect()->route('cart.index');
    }

    // POST /cart/promo   (kode promo atau tombol "Pakai promo")
    public function applyPromo(Request $request)
    {
        $request->validate(['code' => 'required|string|max:30'], ['required' => 'Kode promo belum diisi.']);

        $promo = Promo::where('code', strtoupper(trim($request->code)))->where('is_active', true)->first();
        if (! $promo) {
            return back()->with('error', 'Kode promo tidak ditemukan.');
        }

        $this->cart->selectPromo($promo->code);
        $subtotal = $this->cart->summary()['subtotal'];

        if ($subtotal === 0) {
            return redirect()->back()->with('success', "Promo {$promo->code} dipilih. Pilih menu dulu, promo otomatis dipakai di keranjang.");
        }
        if (! $promo->isEligible($subtotal)) {
            $kurang = number_format($promo->min_order - $subtotal, 0, ',', '.');
            return redirect()->route('cart.index')->with('error', "Belanja Rp {$kurang} lagi untuk memakai promo ini.");
        }

        return redirect()->route('cart.index')->with('success', "Promo {$promo->code} dipakai.");
    }

    // DELETE /cart/promo
    public function removePromo()
    {
        $this->cart->selectPromo(null);

        return back();
    }
}
