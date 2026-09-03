@extends('layouts.app')
@section('title', 'Produk')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Produk & Kategori</h1>
        <p class="text-sm text-slate-500 mt-0.5">{{ $products->count() }} produk · {{ $categories->count() }} kategori</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('kategori.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold px-4 py-2 rounded-lg transition">
            Kelola Kategori
        </a>
        <a href="{{ route('produk.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
            + Tambah Produk
        </a>
    </div>
</div>

{{-- Search & filter --}}
<form method="GET" action="{{ route('produk.index') }}" class="flex flex-wrap gap-3 mb-5">
    <div class="relative flex-1 min-w-[220px]">
        <input name="q" value="{{ $q }}" type="text" placeholder="Cari produk by nama / SKU…"
            class="w-full rounded-lg border-slate-300 pl-10 pr-4 py-2 focus:border-emerald-500 focus:ring-emerald-500">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5 text-slate-400 absolute left-3 top-2.5"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607z"/></svg>
    </div>
    <select name="category" class="rounded-lg border-slate-300 px-3 py-2 focus:border-emerald-500 focus:ring-emerald-500">
        <option value="">Semua Kategori</option>
        @foreach($categories as $c)
        <option value="{{ $c->id }}" {{ $catId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
        @endforeach
    </select>
    <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">Filter</button>
    @if($q !== '' || $catId)
    <a href="{{ route('produk.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-semibold px-4 py-2 rounded-lg transition">Reset</a>
    @endif
</form>

@if(session('success'))
<div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm">
    ✅ {{ session('success') }}
</div>
@endif

<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-slate-50 text-slate-500 border-b border-slate-100">
                <th class="text-left px-4 py-3">SKU</th>
                <th class="text-left px-4 py-3">Nama</th>
                <th class="text-left px-4 py-3">Kategori</th>
                <th class="text-right px-4 py-3">Harga Jual</th>
                <th class="text-right px-4 py-3">Stok</th>
                <th class="text-center px-4 py-3">Status</th>
                <th class="text-right px-4 py-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $p)
            <tr class="border-b border-slate-50 hover:bg-slate-50">
                <td class="px-4 py-3 text-slate-400 font-mono">{{ $p->sku }}</td>
                <td class="px-4 py-3 font-medium">{{ $p->name }}</td>
                <td class="px-4 py-3 text-slate-500">{{ $p->category?->name ?? '-' }}</td>
                <td class="px-4 py-3 text-right">Rp {{ number_format($p->sell_price, 0, ',', '.') }}</td>
                <td class="px-4 py-3 text-right">
                    <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold
                        {{ $p->stock <= $p->min_stock ? 'bg-amber-100 text-amber-700' : 'bg-emerald-50 text-emerald-700' }}">
                        {{ $p->stock }} {{ $p->unit }}
                    </span>
                </td>
                <td class="px-4 py-3 text-center">
                    <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold {{ $p->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                        {{ $p->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('produk.edit', $p) }}" class="text-emerald-600 hover:underline text-sm">Edit</a>
                    <form method="POST" action="{{ route('produk.destroy', $p) }}" class="inline" onsubmit="return confirm('Hapus produk ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-rose-500 hover:underline text-sm ml-2">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
