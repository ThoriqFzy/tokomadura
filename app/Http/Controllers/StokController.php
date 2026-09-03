<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Product;
use App\Services\StockMovementService;
use Illuminate\Http\Request;

class StokController extends Controller
{
    public function __construct(
        private StockMovementService $stockService
    ) {}

    public function index()
    {
        $products = Product::active()->orderBy('name')->get();
        $lowStock = $this->stockService->lowStockProducts();
        $movements = $this->stockService->getMovements(50);

        return view('stok.index', compact('products', 'lowStock', 'movements'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty'        => 'required|integer|min:1',
            'note'       => 'nullable|string|max:255',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        $this->stockService->stockIn(
            $product,
            $validated['qty'],
            $validated['note'] ?? 'Restock dari supplier',
            auth()->id()
        );

        return redirect()->route('stok.index')
            ->with('success', "Stok {$product->name} berhasil ditambahkan sebanyak {$validated['qty']} {$product->unit}.");
    }
}
