<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Promo;
use App\Models\Setting;
use Illuminate\Http\Request;

// Controller halaman depan (BAB II modul Basic CRUD)
class HomeController extends Controller
{
    // GET /  -> landing page: daftar menu + pencarian + filter kategori
    public function index(Request $request)
    {
        $title = 'Mutiara Jaya - Pesan Antar Cepat';
        $categories = Category::orderBy('category_id')->get();

        $products = Product::with('category')
            ->active()
            ->search($request->query('s'))
            ->when($request->filled('category'), fn ($q) => $q->where('category_id', $request->category))
            ->orderBy('product_id')
            ->paginate(12)
            ->withQueryString();   // pagination tetap membawa ?s= dan ?category=

        $activeCategory = $categories->firstWhere('category_id', (int) $request->query('category'));

        return view('frontpage.landingpage', compact('title', 'categories', 'products', 'activeCategory'));
    }

    // GET /product/{product}
    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);

        $product->load('category');
        $title = $product->name;

        // menu lain dari kategori yang sama
        $related = Product::active()
            ->where('category_id', $product->category_id)
            ->where('product_id', '!=', $product->product_id)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return view('frontpage.product', compact('title', 'product', 'related'));
    }

    // GET /tentang  (keterangan toko yang diisi admin)
    public function about()
    {
        $title = 'Tentang Toko';
        $info = [];
        foreach (array_keys(Setting::DEFAULTS) as $key) {
            $info[$key] = Setting::val($key);
        }

        return view('frontpage.about', compact('title', 'info'));
    }

    // GET /promo
    public function promo(\App\Support\Cart $cart)
    {
        $title = 'Promo & Voucher';
        $promos = Promo::where('is_active', true)->orderBy('promo_id')->get();
        $summary = $cart->summary();

        return view('frontpage.promo', compact('title', 'promos', 'summary'));
    }
}
