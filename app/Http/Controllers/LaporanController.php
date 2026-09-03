<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use App\Models\Customer;

class LaporanController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $thisMonth = now()->startOfMonth();

        $stats = [
            'omzetHari'  => (float) Transaction::completed()->whereDate('created_at', $today)->sum('total_amount'),
            'omzetBulan' => (float) Transaction::completed()->where('created_at', '>=', $thisMonth)->sum('total_amount'),
            'totalTransaksi' => Transaction::completed()->whereDate('created_at', $today)->count(),
            'totalPiutang'   => (float) Customer::sum('debt_balance'),
        ];

        return view('laporan.index', compact('stats'));
    }
}
