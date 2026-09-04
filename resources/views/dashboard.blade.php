@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="mb-6">
    @php
        $hour = (int) now()->format('H');
        $greeting = match(true) {
            $hour < 11 => 'Selamat Pagi',
            $hour < 15 => 'Selamat Siang',
            $hour < 18 => 'Selamat Sore',
            default => 'Selamat Malam',
        };
    @endphp
    <h1 class="text-2xl lg:text-3xl font-extrabold text-slate-800">{{ $greeting }}, {{ auth()->user()->name }}</h1>
    <p class="text-sm text-slate-500 mt-1">Ini ringkasan kondisi toko hari ini, <span class="font-medium text-slate-600">{{ now()->translatedFormat('l, d F Y') }}</span></p>
</div>

{{-- Kartu statistik --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-slate-200 p-4 hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div class="text-sm text-slate-500">Total Produk</div>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-emerald-500"><path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75 2.25 12l4.179 2.25m0-4.5 5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0 4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0-5.571 3-5.571-3"/></svg>
        </div>
        <div class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['totalProduk'] }}</div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4 hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div class="text-sm text-slate-500">Stok Kritis</div>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-amber-500"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
        </div>
        <div class="text-2xl font-bold text-amber-600 mt-1">{{ $stats['stokKritis'] }}</div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4 hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div class="text-sm text-slate-500">Total Pelanggan</div>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-blue-500"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
        </div>
        <div class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['totalPelanggan'] }}</div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4 hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div class="text-sm text-slate-500">Piutang Berjalan</div>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-rose-500"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
        </div>
        <div class="text-2xl font-bold text-rose-600 mt-1">Rp {{ number_format($stats['totalPiutang'], 0, ',', '.') }}</div>
    </div>
</div>

{{-- Stok kritis --}}
@if($stats['produkStokRendah']->count())
<div class="bg-white rounded-xl border border-slate-200 p-4">
    <h2 class="font-semibold text-slate-800 mb-3 flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5 text-amber-500"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
        Stok Kritis — Perlu Restock
    </h2>
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
                <tr><td colspan="4" class="py-3 text-center text-emerald-600"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5 inline mr-1 -mt-0.5"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>Semua stok aman</td></tr>
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
