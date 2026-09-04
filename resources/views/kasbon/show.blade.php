@extends('layouts.app')
@section('title', 'Detail Kasbon')

@section('content')
<div class="mb-6">
    <a href="{{ route('kasbon.index') }}" class="text-sm text-emerald-600 hover:underline mb-2 inline-block">← Kembali</a>
    <h1 class="text-2xl font-bold text-slate-800">{{ $customer->name }}</h1>
    <p class="text-sm text-slate-500 mt-0.5">{{ $customer->phone }}</p>
</div>

@if(session('success'))
<div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm">
    ✅ {{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="mb-4 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-lg text-sm">
    <ul>
        @foreach($errors->all() as $e)
        <li>⚠️ {{ $e }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="grid lg:grid-cols-3 gap-5">

    {{-- Panel kiri: saldo & form bayar --}}
    <div class="lg:col-span-1 space-y-4">
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="text-sm text-slate-500">Saldo Piutang</div>
            <div class="text-3xl font-bold mt-1 {{ $customer->debt_balance > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                Rp {{ number_format($customer->debt_balance, 0, ',', '.') }}
            </div>
            @if($customer->debt_balance <= 0)
            <div class="mt-2 text-xs bg-emerald-50 text-emerald-700 px-3 py-1.5 rounded-lg inline-block">✓ Piutang lunas</div>
            @endif
        </div>

        @if($customer->debt_balance > 0)
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h3 class="font-semibold text-slate-700 mb-3 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5 text-emerald-600"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/></svg>
                Terima Pembayaran
            </h3>
            <form method="POST" action="{{ route('kasbon.bayar', $customer->id) }}">
                @csrf
                <div class="mb-3">
                    <label class="text-xs font-semibold text-slate-500 uppercase mb-1 block">Jumlah (maks Rp {{ number_format($customer->debt_balance, 0, ',', '.') }})</label>
                    <input type="number" name="amount" min="1" max="{{ $customer->debt_balance }}" step="0.01" required
                        placeholder="0" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500"
                        value="{{ old('amount') }}">
                </div>
                <div class="mb-4">
                    <label class="text-xs font-semibold text-slate-500 uppercase mb-1 block">Catatan (opsional)</label>
                    <input type="text" name="note" maxlength="255" placeholder="cth: cicilan minggu lalu"
                        class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" value="{{ old('note') }}">
                </div>
                <button type="submit"
                    class="w-full py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm transition">
                    Simpan Pembayaran
                </button>
            </form>
        </div>
        @endif

        {{-- Riwayat pembayaran --}}
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 font-semibold text-slate-700 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5 text-emerald-600"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                Riwayat Pembayaran
            </div>
            @if($customer->debtPayments->count())
            <div class="divide-y divide-slate-50">
                @foreach($customer->debtPayments as $p)
                <div class="px-4 py-2.5 flex items-center justify-between">
                    <div>
                        <div class="text-xs text-slate-400">{{ $p->paid_at->format('d/m/Y H:i') }}</div>
                        <div class="text-xs text-slate-400">oleh {{ $p->recorder?->name ?? '-' }}</div>
                    </div>
                    <div class="text-sm font-bold text-emerald-600 -tracking">− Rp {{ number_format($p->amount, 0, ',', '.') }}</div>
                </div>
                @endforeach
            </div>
            @else
            <div class="p-4 text-center text-slate-400 text-sm">Belum ada pembayaran dicatat.</div>
            @endif
        </div>
    </div>

    {{-- Panel kanan: riwayat transaksi utang --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 font-semibold text-slate-700 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5 text-emerald-600"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"/></svg>
                Riwayat Transaksi Utang
            </div>
            @if($customer->transactions->count())
            <div class="divide-y divide-slate-50">
                @foreach($customer->transactions as $t)
                <div class="px-4 py-3">
                    <div class="flex items-center justify-between">
                        <div class="text-sm font-semibold text-slate-800">#{{ $t->id }}
                            <span class="text-xs font-normal text-slate-400 ml-1">{{ $t->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <span class="text-sm font-bold text-rose-600">+ Rp {{ number_format($t->total_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="mt-1.5 flex flex-wrap gap-1.5">
                        @foreach($t->items as $item)
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full">
                            {{ $item->product->name }} × {{ $item->qty }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="p-6 text-center text-slate-400">Tidak ada transaksi utang untuk pelanggan ini.</div>
            @endif
        </div>
    </div>

</div>
@endsection
