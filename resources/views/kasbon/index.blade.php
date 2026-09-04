@extends('layouts.app')
@section('title', 'Kasbon')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Kasbon Pelanggan</h1>
    <p class="text-sm text-slate-500 mt-0.5">Kelola piutang & riwayat pelunasan</p>
</div>

@if(session('success'))
<div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm">
    ✅ {{ session('success') }}
</div>
@endif

{{-- Kartu statistik --}}
<div class="grid sm:grid-cols-2 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-slate-200 p-4 flex items-center gap-3">
        <div class="w-11 h-11 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"/></svg>
        </div>
        <div>
            <div class="text-xs text-slate-500">Total Piutang</div>
            <div class="text-lg font-bold text-rose-600">Rp {{ number_format($stats['total_piutang'], 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4 flex items-center gap-3">
        <div class="w-11 h-11 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
        </div>
        <div>
            <div class="text-xs text-slate-500">Pelanggan Berpiutang</div>
            <div class="text-lg font-bold text-amber-600">{{ $stats['total_pelanggan'] }} orang</div>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-100 font-semibold text-slate-700">Daftar Pelanggan</div>
    @if($customers->count())
    <div class="divide-y divide-slate-50">
        @foreach($customers as $c)
        <div class="px-4 py-3 flex items-center justify-between gap-3 hover:bg-slate-50">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm shrink-0">
                    {{ strtoupper(substr($c->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <div class="font-medium text-slate-800 truncate">{{ $c->name }}</div>
                    <div class="text-xs text-slate-400">{{ $c->phone }}</div>
                </div>
            </div>
            <div class="flex items-center gap-4 shrink-0">
                <div class="text-right">
                    <div class="text-xs text-slate-400">Piutang</div>
                    <div class="font-bold {{ $c->debt_balance > 0 ? 'text-rose-600' : 'text-slate-400' }}">
                        Rp {{ number_format($c->debt_balance, 0, ',', '.') }}
                    </div>
                </div>
                <a href="{{ route('kasbon.show', $c->id) }}"
                    class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition">
                    Detail
                </a>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="p-6 text-center text-slate-400">Belum ada pelanggan tercatat.</div>
    @endif
</div>
@endsection
