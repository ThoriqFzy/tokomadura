<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DebtPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasbonController extends Controller
{
    /**
     * Daftar pelanggan yang punya piutang (debt_balance > 0).
     */
    public function index()
    {
        $customers = Customer::query()
            ->withSum('debtPayments as total_bayar', 'amount')
            ->orderBy('debt_balance', 'desc')
            ->get();

        $stats = [
            'total_piutang'  => $customers->sum('debt_balance'),
            'total_pelanggan' => $customers->where('debt_balance', '>', 0)->count(),
        ];

        return view('kasbon.index', compact('customers', 'stats'));
    }

    /**
     * Detail piutang seorang pelanggan — riwayat transaksi + riwayat bayar.
     */
    public function show(string $id)
    {
        $customer = Customer::with([
            'transactions' => fn($q) => $q->where('payment_method', 'debt')
                ->with('items.product')
                ->latest(),
            'debtPayments' => fn($q) => $q->latest('paid_at'),
            'debtPayments.recorder',
        ])->findOrFail($id);

        return view('kasbon.show', compact('customer'));
    }

    /**
     * Proses bayar / cicil piutang.
     */
    public function bayarPiutang(Request $request, string $id)
    {
        $customer = Customer::findOrFail($id);

        if ($customer->debt_balance <= 0) {
            return back()->withErrors(['amount' => 'Pelanggan ini tidak memiliki piutang.']);
        }

        $validated = $request->validate([
            'amount' => [
                'required',
                'numeric',
                'min:1',
                'max:' . $customer->debt_balance,
            ],
            'note'   => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($customer, $validated) {
            DebtPayment::create([
                'customer_id' => $customer->id,
                'amount'      => $validated['amount'],
                'paid_at'     => now(),
                'recorded_by' => auth()->id(),
            ]);

            $customer->decrement('debt_balance', $validated['amount']);
        });

        $remaining = $customer->fresh()->debt_balance;

        return back()->with('success', sprintf(
            'Pembayaran Rp %s diterima.%s',
            number_format($validated['amount'], 0, ',', '.'),
            $remaining > 0
                ? " Sisa piutang: Rp " . number_format($remaining, 0, ',', '.')
                : ' Piutang LUNAS! 🎉'
        ));
    }
}
