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
        <h3 class="font-semibold text-slate-800 mb-4">💳 Omzet per Metode</h3>
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
        <h3 class="font-semibold text-slate-800 mb-4">🏆 Produk Terlaris</h3>
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
        <div class="px-4 py-3 border-b border-slate-100 font-semibold text-slate-700">⚠️ Stok Kritis</div>
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
        <div class="px-4 py-3 border-b border-slate-100 font-semibold text-slate-700">
            🧾 Riwayat Transaksi <span class="text-xs font-normal text-slate-400">{{ $start->format('d M') }} – {{ $end->format('d M Y') }}</span>
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
