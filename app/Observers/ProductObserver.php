<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\StockMovement;

class ProductObserver
{
    /**
     * Otomatis log perubahan stok via StockMovementService.
     * Catatan: Fase 3 (POS) akan pakai StockMovementService langsung.
     * Observer ini sebagai safety net — log perubahan yang TIDAK lewat service.
     */
    public function updated(Product $product): void
    {
        if ($product->wasChanged('stock')) {
            $old = $product->getOriginal('stock');
            $new = $product->stock;
            $diff = abs($new - $old);

            if ($diff > 0) {
                // Cegah double-logging jika sudah lewat StockMovementService
                $recentLog = StockMovement::where('product_id', $product->id)
                    ->where('type', $new > $old ? 'in' : 'out')
                    ->where('qty', $diff)
                    ->where('created_at', '>=', now()->subSeconds(5))
                    ->exists();

                if (!$recentLog) {
                    StockMovement::create([
                        'product_id' => $product->id,
                        'type'       => $new > $old ? 'in' : 'out',
                        'qty'        => $diff,
                        'note'       => 'Perubahan stok manual',
                        'created_by' => auth()->id(),
                    ]);
                }
            }
        }
    }
}
