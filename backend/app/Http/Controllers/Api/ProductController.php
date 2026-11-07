<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

// API Resource Controller (BAB III 3.4.5 dan 3.4.6)
class ProductController extends Controller
{
    /**
     * GET /api/products?s=ayam&category_id=1&page=1
     */
    public function index(Request $request)
    {
        $products = Product::with('category')
            ->search($request->query('s'))
            ->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->category_id))
            ->active()
            ->orderBy('product_id')
            ->paginate(10)
            ->withQueryString();

        return ProductResource::collection($products);
    }

    /**
     * POST /api/products  (form-data, khusus admin)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,category_id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'time' => 'nullable|string|max:30',
            'img' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // simpan file ke public/assets/menu, di database cukup path-nya
        $path = null;
        if ($request->hasFile('img')) {
            $path = Product::storeImage($request->file('img'), $request->name);
        }

        $product = Product::create([
            'name' => $request->name,
            'slug' => $this->uniqueSlug($request->name),
            'description' => $request->description,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'stock' => $request->stock,
            'time' => $request->time,
            'img' => $path,
        ]);

        return (new ProductResource($product->load('category')))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * GET /api/products/{id}
     */
    public function show(string $id)
    {
        $product = Product::with('category')->findOrFail($id);

        return new ProductResource($product);
    }

    /**
     * PUT /api/products/{id}  (di Postman: POST + field _method=PUT bila ada upload file)
     */
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,category_id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'time' => 'nullable|string|max:30',
            'is_active' => 'nullable|boolean',
            'img' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only(['name', 'description', 'category_id', 'price', 'stock', 'time']);
        $data['slug'] = $this->uniqueSlug($request->name, $product->product_id);

        if ($request->has('is_active')) {
            $data['is_active'] = $request->boolean('is_active');
        }

        // ganti gambar hanya bila ada file baru, file lama dihapus
        if ($request->hasFile('img')) {
            $product->deleteImageFile();
            $data['img'] = Product::storeImage($request->file('img'), $request->name);
        }

        $product->update($data);

        return new ProductResource($product->load('category'));
    }

    /**
     * DELETE /api/products/{id}
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);

        if ($product == null) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->deleteImageFile();
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
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
