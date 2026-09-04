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
            <h3 class="font-semibold text-slate-700 mb-3">📥 Terima Pembayaran</h3>
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
            <div class="px-4 py-3 border-b border-slate-100 font-semibold text-slate-700">💸 Riwayat Pembayaran</div>
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
            <div class="px-4 py-3 border-b border-slate-100 font-semibold text-slate-700">🧾 Riwayat Transaksi Utang</div>
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
