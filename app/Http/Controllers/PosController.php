<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Services\PosService;
use Illuminate\Http\Request;

class PosController extends Controller
{
    public function __construct(
        private PosService $posService
    ) {}

    public function index()
    {
        $products = Product::active()
            ->with('category')
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get();

        $categories = Category::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();
        $todayTransactions = $this->posService->getTodayTransactions();

        return view('pos.index', compact('products', 'categories', 'customers', 'todayTransactions'));
    }

    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'cart'                  => 'required|array|min:1',
            'cart.*.product_id'     => 'required|exists:products,id',
            'cart.*.qty'            => 'required|integer|min:1',
            'payment_method'        => 'required|in:cash,qris,debt',
            'customer_id'           => 'nullable|exists:customers,id',
            'amount_given'          => 'nullable|numeric|min:0',
        ]);

        try {
            $transaction = $this->posService->checkout(
                $validated['cart'],
                $validated['payment_method'],
                $validated['customer_id'] ?? null,
                (float) ($validated['amount_given'] ?? 0)
            );

            if ($request->wantsJson()) {
                return response()->json([
                    'success'     => true,
                    'message'     => 'Transaksi berhasil disimpan!',
                    'transaction' => $transaction,
                ]);
            }

            return redirect()->route('pos.index')
                ->with('success', "Transaksi #{$transaction->id} berhasil!")
                ->with('receipt_id', $transaction->id);

        } catch (\InvalidArgumentException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function receipt(int $id)
    {
        $transaction = $this->posService->getTransactionDetail($id);
        return view('pos.receipt', compact('transaction'));
    }
}
