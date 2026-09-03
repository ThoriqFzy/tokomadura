@extends('layouts.app')
@section('title', 'Kasbon')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Kasbon Pelanggan</h1>
    <p class="text-sm text-slate-500 mt-0.5">Piutang beredar & riwayat pelunasan</p>
</div>

@if(session('success'))
<div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm">
    ✅ {{ session('success') }}
</div>
@endif

<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    @if($customers->count())
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-slate-50 text-slate-500 border-b border-slate-100">
                <th class="text-left px-4 py-3">Pelanggan</th>
                <th class="text-left px-4 py-3">No HP</th>
                <th class="text-right px-4 py-3">Total Bayar</th>
                <th class="text-right px-4 py-3">Saldo Utang</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $c)
            <tr class="border-b border-slate-50 hover:bg-slate-50">
                <td class="px-4 py-3 font-medium">{{ $c->name }}</td>
                <td class="px-4 py-3 text-slate-500">{{ $c->phone }}</td>
                <td class="px-4 py-3 text-right text-slate-500">Rp {{ number_format($c->total_bayar ?? 0, 0, ',', '.') }}</td>
                <td class="px-4 py-3 text-right font-semibold {{ $c->debt_balance > 0 ? 'text-rose-600' : 'text-slate-400' }}">
                    Rp {{ number_format($c->debt_balance, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="p-6 text-center text-slate-400">Belum ada pelanggan tercatat.</div>
    @endif
</div>
@endsection
