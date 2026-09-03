@extends('layouts.app')
@section('title', 'Edit Produk')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Edit Produk</h1>
    <p class="text-sm text-slate-500 mt-0.5">{{ $produk->name }} ({{ $produk->sku }})</p>
</div>

@if($errors->any())
<div class="mb-4 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-lg text-sm">
    <ul class="list-disc list-inside">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('produk.update', $produk) }}" class="bg-white rounded-xl border border-slate-200 p-6 max-w-2xl space-y-4">
    @csrf @method('PUT')
    <div>
        <label class="text-sm font-semibold text-slate-700">Nama Produk *</label>
        <input name="name" type="text" required value="{{ old('name', $produk->name) }}" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="text-sm font-semibold text-slate-700">SKU *</label>
            <input name="sku" type="text" required value="{{ old('sku', $produk->sku) }}" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
        </div>
        <div>
            <label class="text-sm font-semibold text-slate-700">Kategori *</label>
            <select name="category_id" required class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                @foreach($categories as $c)
                <option value="{{ $c->id }}" {{ $produk->category_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="grid grid-cols-3 gap-4">
        <div>
            <label class="text-sm font-semibold text-slate-700">Satuan *</label>
            <select name="unit" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                @foreach(['pcs','kg','liter','dus'] as $u)
                <option {{ $produk->unit == $u ? 'selected' : '' }}>{{ $u }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm font-semibold text-slate-700">Harga Beli *</label>
            <input name="buy_price" type="number" step="0.01" required value="{{ old('buy_price', $produk->buy_price) }}" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
        </div>
        <div>
            <label class="text-sm font-semibold text-slate-700">Harga Jual *</label>
            <input name="sell_price" type="number" step="0.01" required value="{{ old('sell_price', $produk->sell_price) }}" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
        </div>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="text-sm font-semibold text-slate-700">Stok *</label>
            <input name="stock" type="number" required value="{{ old('stock', $produk->stock) }}" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
        </div>
        <div>
            <label class="text-sm font-semibold text-slate-700">Stok Minimum *</label>
            <input name="min_stock" type="number" required value="{{ old('min_stock', $produk->min_stock) }}" class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
        </div>
    </div>
    <div class="flex gap-3">
        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-5 py-2 rounded-lg transition">Simpan</button>
        <a href="{{ route('produk.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-5 py-2 rounded-lg transition">Batal</a>
    </div>
</form>
@endsection
