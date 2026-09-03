@extends('layouts.app')
@section('title', 'Produk')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Produk & Kategori</h1>
        <p class="text-sm text-slate-500 mt-0.5">{{ $products->count() }} produk · {{ $categories->count() }} kategori</p>
    </div>
    <a href="{{ route('produk.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
        + Tambah Produk
    </a>
</div>

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
