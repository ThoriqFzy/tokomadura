<?php

namespace App\Http\Controllers;

use App\Models\Customer;

class KasbonController extends Controller
{
    public function index()
    {
        $customers = Customer::withSum('debtPayments as total_bayar', 'amount')
            ->orderBy('debt_balance', 'desc')
            ->get();

        return view('kasbon.index', compact('customers'));
    }
}
