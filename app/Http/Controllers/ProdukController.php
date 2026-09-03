<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->query('q', ''));
        $catId = $request->query('category');

        $products = Product::with('category')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                      ->orWhere('sku', 'like', "%{$q}%");
                });
            })
            ->when($catId, function ($query) use ($catId) {
                $query->where('category_id', $catId);
            })
            ->orderBy('name')
            ->get();

        $categories = Category::orderBy('name')->get();

        return view('produk.index', compact('products', 'categories', 'q', 'catId'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('produk.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'sku'         => 'required|string|max:20|unique:products,sku',
            'name'        => 'required|string|max:255',
            'unit'        => 'required|string|max:20',
            'buy_price'   => 'required|numeric|min:0',
            'sell_price'  => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'min_stock'   => 'required|integer|min:0',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        Product::create($validated);

        return redirect()->route('produk.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    public function edit(Product $produk)
    {
        $categories = Category::orderBy('name')->get();
        return view('produk.edit', compact('produk', 'categories'));
    }

    public function update(Request $request, Product $produk)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'sku'         => 'required|string|max:20|unique:products,sku,' . $produk->id,
            'name'        => 'required|string|max:255',
            'unit'        => 'required|string|max:20',
            'buy_price'   => 'required|numeric|min:0',
            'sell_price'  => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'min_stock'   => 'required|integer|min:0',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $produk->update($validated);

        return redirect()->route('produk.index')->with('success', 'Produk berhasil diupdate!');
    }

    public function destroy(Product $produk)
    {
        $produk->delete();
        return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus.');
    }
}
