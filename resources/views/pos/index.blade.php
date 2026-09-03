@extends('layouts.app')
@section('title', 'POS / Kasir')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">POS / Kasir</h1>
    <p class="text-sm text-slate-500 mt-0.5">Transaksi penjualan — {{ now()->format('d M Y, H:i') }}</p>
</div>

@if(session('success'))
<div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm">
    ✅ {{ session('success') }}
</div>
@endif

<div class="bg-white rounded-xl border border-slate-200 p-6">
    <p class="text-slate-600">
        ⚙️ <b>Modul POS sedang dibangun (Fase 3).</b> Fondasi Fase 0 sudah siap.
    </p>
    <p class="text-sm text-slate-500 mt-2">
        {{ $products->count() }} produk aktif · {{ $categories->count() }} kategori tersedia untuk transaksi.
    </p>
    <a href="{{ route('produk.index') }}" class="inline-block mt-4 text-sm text-emerald-600 hover:underline">
        Kelola produk →
    </a>
</div>
@endsection
