@extends('layouts.app')
@section('title', 'Stok')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Manajemen Stok</h1>
    <p class="text-sm text-slate-500 mt-0.5">Posisi stok terkini & pergerakan terbaru</p>
</div>

<div class="grid lg:grid-cols-2 gap-6">
    {{-- Posisi stok --}}
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 font-semibold text-slate-700">Stok Saat Ini</div>
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 text-left">
                    <th class="px-4 py-2">Produk</th>
                    <th class="px-4 py-2 text-right">Stok</th>
                    <th class="px-4 py-2">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $p)
                <tr class="border-b border-slate-50">
                    <td class="px-4 py-2">
                        <div class="font-medium">{{ $p->name }}</div>
                        <div class="text-xs text-slate-400">{{ $p->category?->name }}</div>
                    </td>
                    <td class="px-4 py-2 text-right font-semibold">{{ $p->stock }} {{ $p->unit }}</td>
                    <td class="px-4 py-2">
                        @if($p->stock <= 0)
                        <span class="inline-block px-2 py-0.5 rounded-full text-xs bg-rose-100 text-rose-700">Habis</span>
                        @elseif($p->stock <= $p->min_stock)
                        <span class="inline-block px-2 py-0.5 rounded-full text-xs bg-amber-100 text-amber-700">Minim</span>
                        @else
                        <span class="inline-block px-2 py-0.5 rounded-full text-xs bg-emerald-50 text-emerald-700">Aman</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Riwayat pergerakan --}}
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 font-semibold text-slate-700">Pergerakan Terbaru</div>
        @if($movements->count())
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 text-left">
                    <th class="px-4 py-2">Produk</th>
                    <th class="px-4 py-2">Type</th>
                    <th class="px-4 py-2 text-right">Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach($movements as $m)
                <tr class="border-b border-slate-50">
                    <td class="px-4 py-2 font-medium">{{ $m->product?->name ?? '-' }}</td>
                    <td class="px-4 py-2">
                        <span class="inline-block px-2 py-0.5 rounded-full text-xs {{ $m->type == 'in' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                            {{ $m->type == 'in' ? 'Masuk' : 'Keluar' }}
                        </span>
                    </td>
                    <td class="px-4 py-2 text-right font-semibold">{{ $m->qty }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="p-6 text-center text-slate-400">Belum ada pergerakan.</div>
        @endif
    </div>
</div>
@endsection
