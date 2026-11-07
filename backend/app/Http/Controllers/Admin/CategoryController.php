<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    private array $messages = [
        'required' => 'Kolom :attribute harus diisi',
        'max' => 'Kolom :attribute maksimal :max karakter',
    ];

    public function index(Request $request)
    {
        $title = 'Daftar Kategori';
        $categories = Category::withCount('products')
            ->when($request->filled('s'), fn ($q) => $q->where('category', 'like', '%'.$request->s.'%'))
            ->orderBy('category_id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.categories.index', compact('title', 'categories'));
    }

    public function create()
    {
        $title = 'Tambah Kategori';

        return view('admin.categories.form', compact('title'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['category' => 'required|string|max:100'], $this->messages);
        Category::create($data);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori ditambahkan.');
    }

    public function edit(Category $category)
    {
        $title = 'Edit Kategori '.$category->category;

        return view('admin.categories.form', compact('title', 'category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate(['category' => 'required|string|max:100'], $this->messages);
        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori diperbarui.');
    }

    public function destroy(Category $category)
    {
        // menu di dalam kategori ikut terhapus (onDelete cascade pada migration)
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Kategori dihapus.');
    }
}
