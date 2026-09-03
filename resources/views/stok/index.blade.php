@extends('layouts.app')
@section('title', 'Manajemen Stok')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Manajemen Stok</h1>
    <p class="text-sm text-slate-500 mt-0.5">Posisi stok terkini, input restock & riwayat pergerakan</p>
</div>

@if(session('success'))
<div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm">✅ {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-4 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-lg text-sm">⚠️ {{ session('error') }}</div>
@endif

<div class="grid lg:grid-cols-3 gap-6">

    {{-- === KOLOM KIRI: Input Stok + Alert Kritis === --}}
    <div class="space-y-5">

        {{-- Form input stok masuk --}}
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h2 class="font-semibold text-slate-800 mb-3 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5 text-emerald-600"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                Input Stok Masuk
            </h2>
            <form method="POST" action="{{ route('stok.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="text-sm font-semibold text-slate-700">Produk *</label>
                    <select name="product_id" required class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        <option value="">— Pilih Produk —</option>
                        @foreach($products as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->sku }}) — stok: {{ $p->stock }} {{ $p->unit }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-sm font-semibold text-slate-700">Jumlah *</label>
                        <input name="qty" type="number" min="1" required placeholder="0" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-700">Catatan</label>
                        <input name="note" type="text" placeholder="cth: restock supplier" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    </div>
                </div>
                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition">
                    Tambah Stok
                </button>
            </form>
        </div>

        {{-- Alert stok kritis --}}
        @if($lowStock->count())
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-5">
            <h2 class="font-semibold text-amber-800 mb-2 flex items-center gap-2">
                ⚠️ Stok Kritis ({{ $lowStock->count() }})
            </h2>
            <ul class="space-y-1.5">
                @foreach($lowStock as $p)
                <li class="flex items-center justify-between text-sm">
                    <span class="text-amber-900">{{ $p->name }}</span>
                    <span class="font-bold {{ $p->stock <= 0 ? 'text-rose-600' : 'text-amber-600' }}">
                        {{ $p->stock }}/{{ $p->min_stock }} {{ $p->unit }}
                    </span>
                </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    {{-- === KOLOM KANAN: Tabel stok + Riwayat === --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Posisi stok --}}
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 font-semibold text-slate-700 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5 text-slate-500"><path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                Posisi Stok Saat Ini
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 text-left">
                            <th class="px-4 py-2">Produk</th>
                            <th class="px-4 py-2">Kategori</th>
                            <th class="px-4 py-2 text-right">Stok</th>
                            <th class="px-4 py-2 text-right">Min</th>
                            <th class="px-4 py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $p)
                        <tr class="border-b border-slate-50 {{ $p->stock <= $p->min_stock ? 'bg-amber-50/50' : '' }}">
                            <td class="px-4 py-2.5">
                                <div class="font-medium">{{ $p->name }}</div>
                                <div class="text-xs text-slate-400">{{ $p->sku }}</div>
                            </td>
                            <td class="px-4 py-2.5 text-slate-500">{{ $p->category?->name ?? '-' }}</td>
                            <td class="px-4 py-2.5 text-right font-semibold {{ $p->stock <= 0 ? 'text-rose-600' : ($p->stock <= $p->min_stock ? 'text-amber-600' : 'text-slate-800') }}">
                                {{ $p->stock }}
                            </td>
                            <td class="px-4 py-2.5 text-right text-slate-400">{{ $p->min_stock }}</td>
                            <td class="px-4 py-2.5">
                                @if($p->stock <= 0)
                                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-700">Habis</span>
                                @elseif($p->stock <= $p->min_stock)
                                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Minim</span>
                                @else
                                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">Aman</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-4 py-6 text-center text-slate-400">Belum ada produk aktif.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Riwayat pergerakan --}}
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 font-semibold text-slate-700 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5 text-slate-500"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                Riwayat Pergerakan Stok
            </div>
            @if($movements->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 text-left">
                            <th class="px-4 py-2">Waktu</th>
                            <th class="px-4 py-2">Produk</th>
                            <th class="px-4 py-2">Type</th>
                            <th class="px-4 py-2 text-right">Qty</th>
                            <th class="px-4 py-2">Catatan</th>
                            <th class="px-4 py-2">Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($movements as $m)
                        <tr class="border-b border-slate-50 hover:bg-slate-50">
                            <td class="px-4 py-2 text-xs text-slate-500 whitespace-nowrap">{{ $m->created_at->format('d M H:i') }}</td>
                            <td class="px-4 py-2 font-medium">{{ $m->product?->name ?? '-' }}</td>
                            <td class="px-4 py-2">
                                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold {{ $m->type === 'in' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                    {{ $m->type === 'in' ? '↑ Masuk' : '↓ Keluar' }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-right font-semibold">{{ $m->qty }}</td>
                            <td class="px-4 py-2 text-slate-500 text-xs">{{ $m->note ?? '-' }}</td>
                            <td class="px-4 py-2 text-slate-500 text-xs">{{ $m->creator?->name ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="p-6 text-center text-slate-400">Belum ada pergerakan stok.</div>
            @endif
        </div>
    </div>
</div>
@endsection
