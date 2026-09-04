<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Customer;
use App\Services\StockMovementService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PosService
{
    public function __construct(
        private StockMovementService $stockService
    ) {}

    /**
     * Proses checkout transaksi POS.
     *
     * @param array  $cart          [['product_id' => int, 'qty' => int], ...]
     * @param string $paymentMethod 'cash' | 'qris' | 'debt'
     * @param int|null $customerId  wajib jika 'debt'
     * @param float  $amountGiven   jumlah uang tunai (cash)
     * @return Transaction
     */
    public function checkout(array $cart, string $paymentMethod, ?int $customerId = null, float $amountGiven = 0): Transaction
    {
        if (!in_array($paymentMethod, ['cash', 'qris', 'debt'])) {
            throw new \InvalidArgumentException('Metode pembayaran tidak valid.');
        }

        if ($paymentMethod === 'debt') {
            if (!$customerId) {
                throw new \InvalidArgumentException('Pembayaran utang memerlukan pemilihan pelanggan.');
            }
            $customer = Customer::findOrFail($customerId);
        } else {
            $customer = $customerId ? Customer::find($customerId) : null;
        }

        if ($paymentMethod === 'cash' && $amountGiven < 1) {
            throw new \InvalidArgumentException('Jumlah uang tunai tidak valid.');
        }

        return DB::transaction(function () use ($cart, $paymentMethod, $customer, $amountGiven) {
            $subtotal = 0;
            $itemsData = [];

            // Validasi & kumpulkan data
            foreach ($cart as $item) {
                $product = Product::active()->findOrFail($item['product_id']);
                $qty = (int) $item['qty'];

                if ($qty <= 0) {
                    throw new \InvalidArgumentException("Qty {$product->name} tidak valid.");
                }
                if ($product->stock < $qty) {
                    throw new \InvalidArgumentException("Stok {$product->name} tidak cukup (tersisa {$product->stock}).");
                }

                $lineTotal = $product->sell_price * $qty;
                $subtotal += $lineTotal;

                $itemsData[] = [
                    'product'    => $product,
                    'qty'        => $qty,
                    'sell_price' => $product->sell_price,
                    'line_total' => $lineTotal,
                ];
            }

            $paidAmount = 0;
            $changeAmount = 0;

            if ($paymentMethod === 'cash') {
                if ($amountGiven < $subtotal) {
                    throw new \InvalidArgumentException(
                        "Uang tunai kurang. Butuh Rp " . number_format($subtotal, 0, ',', '.')
                    );
                }
                $paidAmount = $amountGiven;
                $changeAmount = $amountGiven - $subtotal;
            } elseif ($paymentMethod === 'debt') {
                $paidAmount = 0;
                $changeAmount = 0;
            }

            // Buat transaksi
            $transaction = Transaction::create([
                'cashier_id'     => auth()->id(),
                'customer_id'    => $customer?->id,
                'payment_method' => $paymentMethod, // 'cash' | 'qris' | 'debt'
                'total_amount'   => $subtotal,
                'paid_amount'    => $paidAmount,
                'change_amount'  => $changeAmount,
                'status'         => 'completed',
            ]);

            // Buat item + potong stok
            foreach ($itemsData as $data) {
                $product = $data['product'];

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id'     => $product->id,
                    'qty'            => $data['qty'],
                    'price_at_sale'  => $data['sell_price'],
                    'subtotal'       => $data['line_total'],
                ]);

                $this->stockService->stockOut(
                    $product,
                    $data['qty'],
                    "POS #{$transaction->id}",
                    auth()->id()
                );
            }

            // Catat piutang
            if ($paymentMethod === 'debt' && $customer) {
                $customer->increment('debt_balance', $subtotal);
            }

            return $transaction->load(['items.product', 'customer', 'cashier']);
        });
    }

    /**
     * Transaksi hari ini.
     */
    public function getTodayTransactions()
    {
        return Transaction::with(['items.product', 'customer', 'cashier'])
            ->whereDate('created_at', Carbon::today())
            ->where('status', 'completed')
            ->latest()
            ->get();
    }

    /**
     * Detail transaksi untuk cetak struk.
     */
    public function getTransactionDetail(int $id)
    {
        return Transaction::with(['items.product', 'customer', 'cashier'])
            ->findOrFail($id);
    }
}
