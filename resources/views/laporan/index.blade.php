@extends('layouts.app')
@section('title', 'Laporan')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Laporan & Analisis</h1>
    <p class="text-sm text-slate-500 mt-0.5">Akses owner — insight penjualan, stok & piutang</p>
</div>

{{-- Statistik ringkas --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <div class="text-sm text-slate-500">Omzet Hari Ini</div>
        <div class="text-xl font-bold text-slate-800 mt-1">Rp {{ number_format($stats['omzetHari'], 0, ',', '.') }}</div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <div class="text-sm text-slate-500">Omzet Bulan Ini</div>
        <div class="text-xl font-bold text-slate-800 mt-1">Rp {{ number_format($stats['omzetBulan'], 0, ',', '.') }}</div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <div class="text-sm text-slate-500">Transaksi Hari Ini</div>
        <div class="text-xl font-bold text-slate-800 mt-1">{{ $stats['totalTransaksi'] }}</div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <div class="text-sm text-slate-500">Piutang Beredar</div>
        <div class="text-xl font-bold text-rose-600 mt-1">Rp {{ number_format($stats['totalPiutang'], 0, ',', '.') }}</div>
    </div>
</div>

<div class="bg-white rounded-xl border border-slate-200 p-6">
    <p class="text-slate-600">
        📊 <b>Analisis penjualan harian/mingguan/bulanan, produk terlaris & margin akan dibangun di Fase 4.</b>
    </p>
    <p class="text-sm text-slate-500 mt-2">
        Fondasi data (transaksi, stok, piutang) sudah siap dan terisi otomatis dari penggunaan.
    </p>
</div>
@endsection
