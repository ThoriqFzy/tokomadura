@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Dashboard</h1>
    <p class="text-sm text-slate-500 mt-0.5">Ringkasan kondisi toko hari ini</p>
</div>

{{-- Kartu statistik --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <div class="text-sm text-slate-500">Total Produk</div>
        <div class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['totalProduk'] }}</div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <div class="text-sm text-slate-500">Stok Kritis</div>
        <div class="text-2xl font-bold text-amber-600 mt-1">{{ $stats['stokKritis'] }}</div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <div class="text-sm text-slate-500">Total Pelanggan</div>
        <div class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['totalPelanggan'] }}</div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <div class="text-sm text-slate-500">Piutang Berjalan</div>
        <div class="text-2xl font-bold text-rose-600 mt-1">Rp {{ number_format($stats['totalPiutang'], 0, ',', '.') }}</div>
    </div>
</div>

{{-- Stok kritis --}}
@if($stats['produkStokRendah']->count())
<div class="bg-white rounded-xl border border-slate-200 p-4">
    <h2 class="font-semibold text-slate-800 mb-3">⚠️ Stok Kritis — Perlu Restock</h2>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 text-slate-500">
                    <th class="text-left py-2">Produk</th>
                    <th class="text-left py-2">SKU</th>
                    <th class="text-right py-2">Stok</th>
                    <th class="text-right py-2">Min</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stats['produkStokRendah'] as $p)
                <tr class="border-b border-slate-50">
                    <td class="py-2">{{ $p->name }}</td>
                    <td class="py-2 text-slate-400">{{ $p->sku }}</td>
                    <td class="py-2 text-right font-semibold {{ $p->stock <= 0 ? 'text-rose-600' : 'text-amber-600' }}">{{ $p->stock }}</td>
                    <td class="py-2 text-right text-slate-400">{{ $p->min_stock }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="py-3 text-center text-slate-400">Semua stok aman ✅</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($stats['stokKritis'] > 6)
    <div class="text-right mt-2">
        <a href="{{ route('produk.index') }}" class="text-sm text-emerald-600 hover:underline">Lihat semua →</a>
    </div>
    @endif
</div>
@endif
@endsection
