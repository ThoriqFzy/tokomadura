<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;

class PosController extends Controller
{
    public function index()
    {
        $products = Product::active()->with('category')->orderBy('name')->get();
        $categories = Category::withCount('products')->orderBy('name')->get();

        return view('pos.index', compact('products', 'categories'));
    }
}
