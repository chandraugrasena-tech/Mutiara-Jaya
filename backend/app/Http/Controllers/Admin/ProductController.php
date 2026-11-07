<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

// CRUD menu lengkap dengan upload gambar, pencarian, filter, dan pagination
// (mengikuti PetaniController pada modul Backpage)
class ProductController extends Controller
{
    private array $messages = [
        'required' => 'Kolom :attribute harus diisi',
        'numeric' => 'Kolom :attribute harus angka',
        'integer' => 'Kolom :attribute harus bilangan bulat',
        'image' => 'File harus berupa gambar',
        'mimes' => 'Format gambar harus jpg, png, atau webp',
        'max' => 'Ukuran/panjang :attribute melebihi batas (:max)',
        'exists' => 'Kategori tidak valid',
    ];

    public function index(Request $request)
    {
        $title = 'Daftar Menu';
        $categories = Category::orderBy('category_id')->get();

        $products = Product::with('category')
            ->search($request->query('s'))
            ->when($request->filled('category'), fn ($q) => $q->where('category_id', $request->category))
            ->when($request->query('status') === 'aktif', fn ($q) => $q->where('is_active', true))
            ->when($request->query('status') === 'nonaktif', fn ($q) => $q->where('is_active', false))
            ->orderBy('product_id')
            ->paginate(8)
            ->withQueryString();

        return view('admin.products.index', compact('title', 'products', 'categories'));
    }

    public function create()
    {
        $title = 'Tambah Menu';
        $categories = Category::orderBy('category_id')->get();

        return view('admin.products.form', compact('title', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules(), $this->messages);

        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['is_active'] = $request->boolean('is_active');
        $data['img'] = $this->resolveImage($request, null, $data['name']);
        unset($data['img_url']);

        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Menu ditambahkan.');
    }

    public function edit(Product $product)
    {
        $title = 'Edit Menu '.$product->name;
        $categories = Category::orderBy('category_id')->get();

        return view('admin.products.form', compact('title', 'categories', 'product'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate($this->rules(), $this->messages);

        $data['slug'] = $this->uniqueSlug($data['name'], $product->product_id);
        $data['is_active'] = $request->boolean('is_active');
        $data['img'] = $this->resolveImage($request, $product, $data['name']);
        unset($data['img_url']);

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Menu diperbarui.');
    }

    public function destroy(Product $product)
    {
        // hapus file gambar bila tersimpan sebagai file lokal
        $product->deleteImageFile();
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Menu dihapus.');
    }

    private function rules(): array
    {
        return [
            'name' => 'required|string|max:150',
            'category_id' => 'required|exists:categories,category_id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'time' => 'nullable|string|max:30',
            'img' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'img_url' => 'nullable|url|max:600',
        ];
    }

    /**
     * Gambar: file upload didahulukan, lalu url, kalau kosong pakai gambar lama.
     * File upload disimpan ke public/assets/menu (disk 'assets').
     */
    private function resolveImage(Request $request, ?Product $product, string $name): ?string
    {
        $old = $product?->img;

        if ($request->hasFile('img')) {
            $product?->deleteImageFile();
            return Product::storeImage($request->file('img'), $name);
        }

        if ($request->filled('img_url')) {
            $product?->deleteImageFile();
            return $request->input('img_url');
        }

        return $old;
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 2;
        while (Product::where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('product_id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base.'-'.$i++;
        }
        return $slug;
    }
}
