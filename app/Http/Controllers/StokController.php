<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Product;

class StokController extends Controller
{
    public function index()
    {
        $products = Product::active()->orderBy('name')->get();
        $movements = StockMovement::with(['product', 'creator'])
            ->latest()
            ->limit(50)
            ->get();

        return view('stok.index', compact('products', 'movements'));
    }
}
