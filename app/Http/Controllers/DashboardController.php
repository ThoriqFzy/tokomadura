<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Ringkasan dashboard owner.
     * (Data agregasi dasar — tanpa laporan detail. Fase 4 untuk analisis lengkap.)
     */
    public function __invoke()
    {
        $stats = [
            'totalProduk'       => Product::where('is_active', true)->count(),
            'stokKritis'        => Product::whereRaw('stock <= min_stock')->count(),
            'totalPelanggan'    => Customer::count(),
            'totalPiutang'      => (float) Customer::sum('debt_balance'),
            'produkStokRendah'  => Product::whereRaw('stock <= min_stock')->orderBy('stock')->limit(6)->get(),
        ];

        return view('dashboard', compact('stats'));
    }
}
