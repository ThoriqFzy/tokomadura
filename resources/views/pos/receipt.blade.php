<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $transaction->id }} — Toko Madura</title>
    <style>
        body { font-family: 'Courier New', monospace; font-size: 13px; max-width: 320px; margin: 0 auto; padding: 16px; color: #000; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .border-top { border-top: 1px dashed #000; margin: 8px 0; padding-top: 8px; }
        .border-bottom { border-bottom: 1px dashed #000; margin: 8px 0; padding-bottom: 8px; }
        table { width: 100%; }
        td { padding: 2px 0; }
        .right { text-align: right; }
        @media print {
            body { margin: 0; padding: 8px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div class="center">
    <div class="bold" style="font-size:16px">TOKO MADURA</div>
    <div>Sumber Rejeki</div>
    <div style="font-size:11px">Jl. Contoh No. 123, Madura</div>
</div>

<div class="border-top center">Struk Pembelian</div>

<table>
    <tr><td>No.</td><td>: #{{ $transaction->id }}</td></tr>
    <tr><td>Tanggal</td><td>: {{ $transaction->created_at->format('d/m/Y H:i') }}</td></tr>
    <tr><td>Kasir</td><td>: {{ $transaction->cashier->name }}</td></tr>
    @if($transaction->customer)
    <tr><td>Pelanggan</td><td>: {{ $transaction->customer->name }}</td></tr>
    @endif
    <tr><td>Bayar</td><td>: {{ strtoupper($transaction->payment_method) }}</td></tr>
</table>

<div class="border-top"></div>

<table>
    @foreach($transaction->items as $item)
    <tr>
        <td>{{ $item->product->name }}<br><span style="font-size:11px">{{ $item->qty }} × Rp{{ number_format($item->price_at_sale, 0, ',', '.') }}</span></td>
        <td class="right bold">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
    </tr>
    @endforeach
</table>

<div class="border-bottom"></div>

<table>
    <tr><td class="bold">TOTAL</td><td class="right bold" style="font-size:15px">Rp{{ number_format($transaction->total_amount, 0, ',', '.') }}</td></tr>
    @if($transaction->payment_method === 'cash')
    <tr><td>Tunai</td><td class="right">Rp{{ number_format($transaction->amount_given, 0, ',', '.') }}</td></tr>
    <tr><td>Kembali</td><td class="right">Rp{{ number_format($transaction->change, 0, ',', '.') }}</td></tr>
    @endif
    @if($transaction->payment_method === 'utang')
    <tr><td>Sisa Utang</td><td class="right bold">Rp{{ number_format($transaction->customer->debt_balance, 0, ',', '.') }}</td></tr>
    @endif
</table>

<div class="border-bottom center" style="font-size:11px">Terima kasih atas kunjungan Anda!</div>

<div class="center no-print" style="margin-top:16px">
    <button onclick="window.print()" style="padding:8px 24px; background:#059669; color:#fff; border:none; border-radius:8px; cursor:pointer; font-size:14px;">🖨️ Cetak Struk</button>
</div>

</body>
</html>
