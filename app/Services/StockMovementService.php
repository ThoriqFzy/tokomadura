<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class StockMovementService
{
    /**
     * Input stok masuk (restock dari supplier).
     */
    public function stockIn(Product $product, int $qty, string $note, int $userId): StockMovement
    {
        if ($qty <= 0) {
            throw new \InvalidArgumentException('Jumlah stok masuk harus lebih dari 0.');
        }

        return DB::transaction(function () use ($product, $qty, $note, $userId) {
            $product->increment('stock', $qty);

            return StockMovement::create([
                'product_id' => $product->id,
                'type'       => 'in',
                'qty'        => $qty,
                'note'       => $note,
                'created_by' => $userId,
            ]);
        });
    }

    /**
     * Kurangi stok saat transaksi jual.
     */
    public function stockOut(Product $product, int $qty, string $note, int $userId): StockMovement
    {
        if ($qty <= 0) {
            throw new \InvalidArgumentException('Jumlah stok keluar harus lebih dari 0.');
        }

        if ($product->stock < $qty) {
            throw new \InvalidArgumentException("Stok {$product->name} tidak cukup (tersisa {$product->stock}).");
        }

        return DB::transaction(function () use ($product, $qty, $note, $userId) {
            $product->decrement('stock', $qty);

            return StockMovement::create([
                'product_id' => $product->id,
                'type'       => 'out',
                'qty'        => $qty,
                'note'       => $note,
                'created_by' => $userId,
            ]);
        });
    }

    /**
     * Stok kritis: di bawah minimum.
     */
    public function lowStockProducts()
    {
        return Product::active()
            ->whereColumn('stock', '<=', 'min_stock')
            ->orderBy('stock')
            ->get();
    }

    /**
     * Riwayat pergerakan stok (terbaru dulu).
     */
    public function getMovements(int $limit = 50)
    {
        return StockMovement::with(['product', 'creator'])
            ->latest()
            ->limit($limit)
            ->get();
    }
}
