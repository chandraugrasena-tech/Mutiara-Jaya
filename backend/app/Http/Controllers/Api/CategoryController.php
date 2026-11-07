<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return CategoryResource::collection(
            Category::withCount('products')->orderBy('category_id')->get()
        );
    }

    public function show(string $id)
    {
        return new CategoryResource(Category::withCount('products')->findOrFail($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['category' => 'required|string|max:100']);

        return (new CategoryResource(Category::create($data)))
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);
        $category->update($request->validate(['category' => 'required|string|max:100']));

        return new CategoryResource($category);
    }

    public function destroy(string $id)
    {
        $category = Category::find($id);
        if ($category == null) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }
}
