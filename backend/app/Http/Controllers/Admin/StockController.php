<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Models\StockLog;
use Illuminate\Http\Request;

// Stok barang: lihat stok, tambah, kurangi, dan riwayatnya
class StockController extends Controller
{
    // GET /admin/stok?s=&category=&level=
    public function index(Request $request)
    {
        $title = 'Stok Barang';
        $limit = (int) Setting::val('low_stock_limit');
        $categories = Category::orderBy('category_id')->get();

        $products = Product::with('category')
            ->search($request->query('s'))
            ->when($request->filled('category'), fn ($q) => $q->where('category_id', $request->category))
            ->when($request->query('level') === 'menipis', fn ($q) => $q->where('stock', '>', 0)->where('stock', '<=', $limit))
            ->when($request->query('level') === 'habis', fn ($q) => $q->where('stock', '<=', 0))
            ->orderBy('stock')
            ->orderBy('product_id')
            ->paginate(12)
            ->withQueryString();

        $logs = StockLog::with(['product', 'user'])->latest('stock_log_id')->limit(10)->get();

        return view('admin.stock.index', compact('title', 'products', 'categories', 'limit', 'logs'));
    }

    // PATCH /admin/stok/{product}   (action = tambah / kurangi)
    public function adjust(Request $request, Product $product)
    {
        $data = $request->validate([
            'amount' => 'required|integer|min:1|max:100000',
            'action' => 'required|in:tambah,kurangi',
            'note' => 'nullable|string|max:80',
        ], [
            'amount.required' => 'Jumlah stok harus diisi',
            'amount.integer' => 'Jumlah stok harus berupa angka bulat',
            'amount.min' => 'Jumlah stok minimal 1',
        ]);

        $delta = $data['action'] === 'tambah' ? $data['amount'] : -$data['amount'];
        $reason = ($data['action'] === 'tambah' ? 'Tambah stok' : 'Kurangi stok')
            .(! empty($data['note']) ? ': '.$data['note'] : '');

        $product->adjustStock($delta, $reason, $request->user()->id);

        return back()->with('success', 'Stok '.$product->name.' sekarang '.$product->stock.'.');
    }
}
