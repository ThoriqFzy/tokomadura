<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LaporanController extends Controller
{
    /**
     * Laporan & analisis penjualan.
     */
    public function index(Request $request)
    {
        // Tentukan rentang tanggal
        $range = $request->input('range', 'today'); // today | week | month | custom
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        [$start, $end] = $this->resolveRange($range, $startDate, $endDate);

        // Transaksi dalam rentang
        $transactions = Transaction::completed()
            ->whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()])
            ->with(['customer', 'cashier'])
            ->latest();

        // Omzet per metode
        $omzetByMethod = (clone $transactions)->get()
            ->groupBy('payment_method')
            ->map(fn($g) => (float) $g->sum('total_amount'));

        // Produk terlaris (by qty)
        $topProductsQty = TransactionItem::whereHas('transaction', function ($q) use ($start, $end) {
            $q->completed()->whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()]);
        })
            ->selectRaw('product_id, SUM(qty) as total_qty, SUM(subtotal) as total_omzet')
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // Stok kritis
        $lowStock = Product::active()->whereColumn('stock', '<=', 'min_stock')->get();

        // Statistik ringkas
        $stats = [
            'omzet'        => (float) (clone $transactions)->sum('total_amount'),
            'jumlah'       => (clone $transactions)->count(),
            'tunai'        => $omzetByMethod['cash'] ?? 0,
            'qris'         => $omzetByMethod['qris'] ?? 0,
            'utang'        => $omzetByMethod['debt'] ?? 0,
            'piutang'      => (float) Customer::sum('debt_balance'),
            'lowStockCount'=> $lowStock->count(),
            'avgPerTrans'  => (clone $transactions)->count() > 0
                ? ((clone $transactions)->sum('total_amount') / (clone $transactions)->count())
                : 0,
        ];

        $transactionsList = $transactions->get();

        return view('laporan.index', compact(
            'stats', 'transactionsList', 'topProductsQty', 'lowStock',
            'range', 'start', 'end'
        ));
    }

    /**
     * Selesaikan rentang tanggal berdasarkan pilihan range.
     */
    private function resolveRange(string $range, ?string $startDate, ?string $endDate): array
    {
        $today = Carbon::today();

        return match ($range) {
            'week'   => [$today->copy()->startOfWeek(), $today->copy()->endOfWeek()],
            'month'  => [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()],
            'custom' => [
                $startDate ? Carbon::parse($startDate) : $today->copy()->subDays(6),
                $endDate ? Carbon::parse($endDate) : $today,
            ],
            default  => [$today->copy(), $today->copy()], // today
        };
    }
}
