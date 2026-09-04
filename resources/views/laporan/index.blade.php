@extends('layouts.app')
@section('title', 'Laporan')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Laporan & Analisis</h1>
    <p class="text-sm text-slate-500 mt-0.5">Omzet, produk terlaris, stok & piutang</p>
</div>

{{-- Filter --}}
<form method="GET" action="{{ route('laporan.index') }}" class="bg-white rounded-xl border border-slate-200 p-4 mb-6 flex flex-wrap items-end gap-3">
    <div>
        <label class="text-xs font-semibold text-slate-500 uppercase mb-1 block">Rentang</label>
        <select name="range" id="range" class="rounded-lg border-slate-300 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500" onchange="toggleCustom()">
            <option value="today" {{ $range=='today' ? 'selected' : '' }}>Hari Ini</option>
            <option value="week" {{ $range=='week' ? 'selected' : '' }}>Minggu Ini</option>
            <option value="month" {{ $range=='month' ? 'selected' : '' }}>Bulan Ini</option>
            <option value="custom" {{ $range=='custom' ? 'selected' : '' }}>Kustom</option>
        </select>
    </div>
    <div id="customWrap" class="flex gap-2 {{ $range=='custom' ? '' : 'hidden' }}">
        <div>
            <label class="text-xs font-semibold text-slate-500 uppercase mb-1 block">Dari</label>
            <input type="date" name="start_date" value="{{ request('start_date', $start->format('Y-m-d')) }}" class="rounded-lg border-slate-300 py-2.5 text-sm">
        </div>
        <div>
            <label class="text-xs font-semibold text-slate-500 uppercase mb-1 block">Sampai</label>
            <input type="date" name="end_date" value="{{ request('end_date', $end->format('Y-m-d')) }}" class="rounded-lg border-slate-300 py-2.5 text-sm">
        </div>
    </div>
    <button type="submit" class="px-4 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold transition">Terapkan</button>
</form>

<script>
function toggleCustom() {
    const custom = document.getElementById('range').value === 'custom';
    document.getElementById('customWrap').classList.toggle('hidden', !custom);
}
</script>

{{-- Statistik --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <div class="text-sm text-slate-500">Omzet</div>
        <div class="text-xl font-bold text-slate-800 mt-1">Rp {{ number_format($stats['omzet'], 0, ',', '.') }}</div>
        <div class="text-xs text-slate-400 mt-0.5">{{ $stats['jumlah'] }} transaksi</div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <div class="text-sm text-slate-500">Rata-rata / Transaksi</div>
        <div class="text-xl font-bold text-slate-800 mt-1">Rp {{ number_format($stats['avgPerTrans'], 0, ',', '.') }}</div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <div class="text-sm text-slate-500">Stok Kritis</div>
        <div class="text-xl font-bold {{ $stats['lowStockCount']>0 ? 'text-amber-600' : 'text-slate-800' }} mt-1">{{ $stats['lowStockCount'] }} produk</div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <div class="text-sm text-slate-500">Piutang Beredar</div>
        <div class="text-xl font-bold text-rose-600 mt-1">Rp {{ number_format($stats['piutang'], 0, ',', '.') }}</div>
    </div>
</div>

{{-- Omzet per metode + Produk terlaris --}}
<div class="grid lg:grid-cols-2 gap-5 mb-6">
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <h3 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5 text-emerald-600"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"/></svg>
            Omzet per Metode
        </h3>
        @php
            $methods = [
                'cash' => ['Tunai', 'text-emerald-600', $stats['tunai']],
                'qris' => ['QRIS', 'text-blue-600', $stats['qris']],
                'debt' => ['Utang', 'text-amber-600', $stats['utang']],
            ];
            $max = max(1, $stats['tunai'], $stats['qris'], $stats['utang']);
        @endphp
        <div class="space-y-3">
            @foreach($methods as $m)
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-slate-600">{{ $m[0] }}</span>
                    <span class="font-semibold {{ $m[1] }}">Rp {{ number_format($m[2], 0, ',', '.') }}</span>
                </div>
                <div class="h-2.5 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full rounded-full {{ $m[1] }}" style="width: {{ ($m[2]/$max)*100 }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <h3 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5 text-emerald-600"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0M10.5 2.25v.75m3-0.75v.75"/></svg>
            Produk Terlaris
        </h3>
        @if($topProductsQty->count())
        <div class="space-y-3">
            @foreach($topProductsQty as $i => $top)
            <div class="flex items-center gap-3">
                <div class="w-7 h-7 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-xs font-bold shrink-0">{{ $i+1 }}</div>
                <div class="min-w-0 flex-1">
                    <div class="text-sm font-medium text-slate-800 truncate">{{ $top->product->name }}</div>
                    <div class="text-xs text-slate-400">{{ $top->total_qty }} terjual</div>
                </div>
                <div class="text-sm font-semibold text-slate-700">Rp {{ number_format($top->total_omzet, 0, ',', '.') }}</div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center text-slate-400 text-sm py-6">Belum ada penjualan pada rentang ini.</div>
        @endif
    </div>
</div>

{{-- Stok kritis + Riwayat transaksi --}}
<div class="grid lg:grid-cols-3 gap-5 mb-6">
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 font-semibold text-slate-700 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5 text-amber-500"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
            Stok Kritis
        </div>
        @if($lowStock->count())
        <div class="divide-y divide-slate-50">
            @foreach($lowStock as $p)
            <div class="px-4 py-2.5 flex items-center justify-between">
                <div class="text-sm font-medium text-slate-800 truncate mr-2">{{ $p->name }}</div>
                <span class="text-xs px-2 py-0.5 rounded-full {{ $p->stock<=0 ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700' }} shrink-0">
                    {{ $p->stock }} / min {{ $p->min_stock }}
                </span>
            </div>
            @endforeach
        </div>
        @else
        <div class="p-4 text-center text-emerald-600 text-sm">✓ Semua stok aman</div>
        @endif
    </div>

    <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 font-semibold text-slate-700 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5 text-emerald-600"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"/></svg>
            Riwayat Transaksi <span class="text-xs font-normal text-slate-400">{{ $start->format('d M') }} – {{ $end->format('d M Y') }}</span>
        </div>
        @if($transactionsList->count())
        <div class="max-h-96 overflow-y-auto divide-y divide-slate-50">
            @foreach($transactionsList as $t)
            <div class="px-4 py-2.5 flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <div class="text-sm font-semibold text-slate-800">#{{ $t->id }}
                        <span class="text-xs font-normal text-slate-400 ml-1">{{ $t->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="text-xs text-slate-500">
                        {{ strtoupper($t->payment_method) }}
                        @if($t->customer) · {{ $t->customer->name }} @endif
                        @if($t->cashier) · {{ $t->cashier->name }} @endif
                    </div>
                </div>
                <div class="text-sm font-bold text-slate-800 shrink-0">Rp {{ number_format($t->total_amount, 0, ',', '.') }}</div>
            </div>
            @endforeach
        </div>
        @else
        <div class="p-6 text-center text-slate-400 text-sm">Tidak ada transaksi pada rentang ini.</div>
        @endif
    </div>
</div>
@endsection
