@extends('layouts.app')
@section('title', 'POS / Kasir')

@section('content')
<div x-data="posApp()" x-init="init()" class="grid lg:grid-cols-5 gap-5">

    {{-- === KOLOM KIRI: Daftar Produk (3/5) === --}}
    <div class="lg:col-span-3 space-y-4">
        {{-- Search + filter kategori --}}
        <div class="flex flex-wrap gap-3">
            <div class="relative flex-1 min-w-[200px]">
                <input type="text" x-model="search" placeholder="Cari produk / SKU…"
                    class="w-full rounded-lg border-slate-300 pl-10 pr-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5 text-slate-400 absolute left-3 top-2.5"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607z"/></svg>
            </div>
            <select x-model="filterCat" class="rounded-lg border-slate-300 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                <option value="">Semua</option>
                @foreach($categories as $c)
                <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Grid produk --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3">
            <template x-for="p in filteredProducts" :key="p.id">
                <button type="button" @click="addToCart(p)"
                    class="bg-white rounded-xl border border-slate-200 p-3 text-left hover:border-emerald-400 hover:shadow-md transition focus:ring-2 focus:ring-emerald-400 focus:outline-none">
                    <div class="w-full aspect-square bg-slate-50 rounded-lg flex items-center justify-center mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-slate-300"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                    </div>
                    <div class="font-semibold text-sm text-slate-800 truncate" x-text="p.name"></div>
                    <div class="text-xs text-slate-400 mt-0.5" x-text="p.sku"></div>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-emerald-700 font-bold text-sm" x-text="'Rp' + num(p.sell_price)"></span>
                        <span class="text-xs px-1.5 py-0.5 rounded-full"
                            :class="p.stock <= p.min_stock ? (p.stock <= 0 ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') : 'bg-slate-100 text-slate-500'"
                            x-text="p.stock + ' ' + p.unit"></span>
                    </div>
                </button>
            </template>
            <template x-if="filteredProducts.length === 0">
                <div class="col-span-full py-12 text-center text-slate-400">Produk tidak ditemukan.</div>
            </template>
        </div>
    </div>

    {{-- === KOLOM KANAN: Keranjang + Pembayaran (2/5) === --}}
    <div class="lg:col-span-2 space-y-4">
    <div class="bg-white rounded-xl border border-slate-200 sticky top-20 space-y-0 overflow-hidden">

            {{-- Header keranjang --}}
            <div class="px-4 py-3 bg-emerald-600 text-white flex items-center justify-between">
                <span class="font-bold text-sm">🛒 Keranjang (<span x-text="cartCount"></span>)</span>
                <button type="button" @click="clearCart()" x-show="cart.length > 0" class="text-emerald-100 hover:text-white text-xs">Kosongkan</button>
            </div>

            {{-- Items --}}
            <div class="max-h-[340px] overflow-y-auto divide-y divide-slate-100">
                <template x-if="cart.length === 0">
                    <div class="py-10 text-center text-slate-400 text-sm">Belum ada produk dipilih.</div>
                </template>
                <template x-for="(item, idx) in cart" :key="item.id">
                    <div class="px-4 py-3 flex items-center gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-semibold text-slate-800 truncate" x-text="item.name"></div>
                            <div class="text-xs text-slate-400" x-text="'Rp' + num(item.sell_price) + ' × ' + item.qty"></div>
                        </div>
                        <div class="flex items-center gap-1">
                            <button type="button" @click="changeQty(idx, -1)" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-sm font-bold">−</button>
                            <span class="w-8 text-center text-sm font-semibold" x-text="item.qty"></span>
                            <button type="button" @click="changeQty(idx, 1)" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-sm font-bold">+</button>
                        </div>
                        <div class="text-sm font-bold text-slate-800 w-24 text-right" x-text="'Rp' + num(item.sell_price * item.qty)"></div>
                        <button type="button" @click="removeItem(idx)" class="text-rose-400 hover:text-rose-600 ml-1">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </template>
            </div>

            {{-- Total --}}
            <div class="px-4 py-3 bg-slate-50 border-t border-slate-200">
                <div class="flex justify-between items-center mb-1">
                    <span class="text-sm text-slate-500">Subtotal</span>
                    <span class="font-bold text-lg text-slate-800" x-text="'Rp' + num(grandTotal)"></span>
                </div>
            </div>

            {{-- Metode pembayaran --}}
            <div class="px-4 py-4 space-y-4 border-t border-slate-200">
                {{-- Pelanggan (jika utang) --}}
                <div x-show="paymentMethod === 'debt'" x-transition>
                    <label class="text-xs font-semibold text-slate-500 uppercase mb-1 block">Pilih Pelanggan *</label>
                    <select x-model="customerId" class="w-full rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">— Pilih —</option>
                        @foreach($customers as $c)
                        <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->phone }})</option>
                        @endforeach
                    </select>
                </div>

                {{-- Input uang tunai --}}
                <div x-show="paymentMethod === 'cash'" x-transition>
                    <label class="text-xs font-semibold text-slate-500 uppercase mb-1 block">Uang Diterima</label>
                    <input type="number" x-model.number="amountGiven" min="0" placeholder="0"
                        class="w-full rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" inputmode="numeric">
                    <div x-show="amountGiven >= grandTotal && grandTotal > 0" class="mt-1 text-sm text-emerald-600 font-semibold">
                        Kembalian: <span x-text="'Rp' + num(amountGiven - grandTotal)"></span>
                    </div>
                    <div x-show="amountGiven > 0 && amountGiven < grandTotal" class="mt-1 text-sm text-rose-600 font-semibold">
                        Kurang: <span x-text="'Rp' + num(grandTotal - amountGiven)"></span>
                    </div>
                </div>

                {{-- Tombol tampilkan QRIS (hanya metode qris) --}}
                <div x-show="paymentMethod === 'qris'" x-transition class="text-center">
                    <div class="text-xs text-slate-500 mb-2">Arahkan QRIS pelanggan ke poster berikut:</div>
                    <button type="button" @click="showQris = true"
                        class="w-full grid place-items-center gap-2 py-4 rounded-lg border-2 border-emerald-500 bg-emerald-50 hover:bg-emerald-100 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-emerald-600"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5ZM13.5 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5Z"/></svg>
                        <span class="font-semibold text-emerald-700 text-sm">Tampilkan QRIS Thor Store</span>
                    </button>
                </div>

                {{-- Tombol metode --}}
                <div class="grid grid-cols-3 gap-2">
                    <button type="button" @click="paymentMethod = 'cash'" class="py-2.5 rounded-lg text-sm font-semibold border-2 transition"
                        :class="paymentMethod === 'cash' ? 'border-emerald-500 bg-emerald-50 text-emerald-700' : 'border-slate-200 text-slate-600 hover:border-slate-300'">💵 Tunai</button>
                    <button type="button" @click="paymentMethod = 'qris'" class="py-2.5 rounded-lg text-sm font-semibold border-2 transition"
                        :class="paymentMethod === 'qris' ? 'border-emerald-500 bg-emerald-50 text-emerald-700' : 'border-slate-200 text-slate-600 hover:border-slate-300'">📱 QRIS</button>
                    <button type="button" @click="paymentMethod = 'debt'" class="py-2.5 rounded-lg text-sm font-semibold border-2 transition"
                        :class="paymentMethod === 'debt' ? 'border-amber-500 bg-amber-50 text-amber-700' : 'border-slate-200 text-slate-600 hover:border-slate-300'">📝 Utang</button>
                </div>

                {{-- Tombol proses --}}
                <button type="button" @click="checkout()" :disabled="!canCheckout || submitting"
                    class="w-full py-3 rounded-lg text-white font-bold text-sm transition disabled:opacity-40 disabled:cursor-not-allowed"
                    :class="paymentMethod === 'debt' ? 'bg-amber-500 hover:bg-amber-600' : 'bg-emerald-600 hover:bg-emerald-700'">
                    <span x-show="!submitting">Proses Pembayaran</span>
                    <span x-show="submitting">Memproses…</span>
                </button>

                {{-- Error --}}
                <div x-show="errorMsg" x-transition class="text-sm text-rose-600 bg-rose-50 rounded-lg px-3 py-2" x-text="errorMsg"></div>

                {{-- Sukses --}}
                <div x-show="successMsg" x-transition class="text-sm text-emerald-700 bg-emerald-50 rounded-lg px-3 py-2">
                    <span x-text="successMsg"></span>
                    <a x-show="receiptId" :href="'{{ url("pos/receipt") }}/' + receiptId" target="_blank" class="ml-2 underline font-semibold">Cetak Struk →</a>
                </div>
                </div>
        </div>

        {{-- Riwayat transaksi hari ini --}}
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 font-semibold text-slate-700 flex items-center justify-between">
                <span>🧾 Transaksi Hari Ini</span>
                <span class="text-xs font-normal text-slate-400">{{ $todayTransactions->count() }} transaksi</span>
            </div>
            @if($todayTransactions->count())
            <div class="max-h-64 overflow-y-auto divide-y divide-slate-50">
                @foreach($todayTransactions as $t)
                <div class="px-4 py-2.5 flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <div class="text-sm font-semibold text-slate-800">#{{ $t->id }}
                            <span class="text-xs font-normal text-slate-400 ml-1">{{ $t->created_at->format('H:i') }}</span>
                        </div>
                        <div class="text-xs text-slate-500 truncate">
                            {{ strtoupper($t->payment_method) }}
                            @if($t->customer) · {{ $t->customer->name }} @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-bold text-slate-800">Rp{{ number_format($t->total_amount, 0, ',', '.') }}</span>
                        <a href="{{ route('pos.receipt', $t->id) }}" target="_blank" class="text-emerald-600 hover:text-emerald-700" title="Cetak struk">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Z"/></svg>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="p-5 text-center text-slate-400 text-sm">Belum ada transaksi hari ini.</div>
            @endif
        </div>
    </div>
</div>

{{-- Modal QRIS --}}
<div x-show="showQris" x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
    @keydown.escape.window="showQris = false">
    <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full overflow-hidden" @click.outside="showQris = false">
        <div class="px-4 py-3 bg-emerald-600 text-white flex items-center justify-between">
            <span class="font-bold text-sm">📱 Scan QRIS</span>
            <button type="button" @click="showQris = false" class="text-emerald-100 hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-5">
            <img src="{{ asset('img/qris-thor.jpg') }}" alt="QRIS Thor Store" class="w-full rounded-lg border border-slate-200 shadow-inner">
            <div class="mt-3 text-center text-sm text-slate-600">
                <span class="font-semibold text-slate-800">Thor Store</span> · NMID ID1026529839122 · A01
            </div>
            <div class="mt-1 text-center text-xs text-slate-400">Minta pelanggan scan & konfirmasi nominal sebelum bayar.</div>
        </div>
    </div>
</div>

<script>
window.posApp = function() {
    return {
        allProducts: @json($products),
        search: '',
        filterCat: '',
        cart: [],
        paymentMethod: 'cash',
        customerId: '',
        amountGiven: 0,
        submitting: false,
        errorMsg: '',
        successMsg: '',
        receiptId: null,
        showQris: false,

        init() {},

        get filteredProducts() {
            return this.allProducts.filter(p => {
                const matchSearch = !this.search || p.name.toLowerCase().includes(this.search.toLowerCase()) || p.sku.toLowerCase().includes(this.search.toLowerCase());
                const matchCat = !this.filterCat || p.category_id == this.filterCat;
                return matchSearch && matchCat;
            });
        },

        get cartCount() {
            return this.cart.reduce((sum, i) => sum + i.qty, 0);
        },

        get grandTotal() {
            return this.cart.reduce((sum, i) => sum + (i.sell_price * i.qty), 0);
        },

        get canCheckout() {
            if (this.cart.length === 0) return false;
            if (this.paymentMethod === 'cash' && this.amountGiven < this.grandTotal) return false;
            if (this.paymentMethod === 'debt' && !this.customerId) return false;
            return true;
        },

        addToCart(product) {
            const existing = this.cart.find(i => i.id === product.id);
            if (existing) {
                if (existing.qty >= product.stock) { this.errorMsg = 'Stok ' + product.name + ' tidak cukup!'; setTimeout(() => this.errorMsg = '', 3000); return; }
                existing.qty++;
            } else {
                if (product.stock <= 0) { this.errorMsg = 'Stok ' + product.name + ' habis!'; setTimeout(() => this.errorMsg = '', 3000); return; }
                this.cart.push({ id: product.id, name: product.name, sell_price: product.sell_price, stock: product.stock, unit: product.unit, qty: 1 });
            }
            this.errorMsg = '';
        },

        changeQty(idx, delta) {
            const item = this.cart[idx];
            const newQty = item.qty + delta;
            if (newQty <= 0) { this.cart.splice(idx, 1); return; }
            if (newQty > item.stock) { this.errorMsg = 'Stok ' + item.name + ' tidak cukup!'; setTimeout(() => this.errorMsg = '', 3000); return; }
            item.qty = newQty;
        },

        removeItem(idx) { this.cart.splice(idx, 1); },
        clearCart() { this.cart = []; },

        async checkout() {
            if (!this.canCheckout) return;
            this.submitting = true;
            this.errorMsg = '';
            this.successMsg = '';
            this.receiptId = null;

            try {
                const payload = {
                    cart: this.cart.map(i => ({ product_id: i.id, qty: i.qty })),
                    payment_method: this.paymentMethod,
                    customer_id: this.customerId || null,
                    amount_given: this.paymentMethod === 'cash' ? this.amountGiven : 0,
                };
                const res = await fetch('{{ route("pos.checkout") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                    body: JSON.stringify(payload),
                });
                const data = await res.json();
                if (data.success) {
                    this.successMsg = data.message;
                    this.receiptId = data.transaction.id;
                    this.cart = [];
                    this.amountGiven = 0;
                    this.customerId = '';
                    // Reload products to get updated stock
                    const prodRes = await fetch('{{ route("pos.index") }}', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                    if (prodRes.ok) {
                        const html = await prodRes.text();
                        const match = html.match(/allProducts:\s*(\[.*?\])/s);
                        if (match) this.allProducts = JSON.parse(match[1]);
                    }
                } else {
                    this.errorMsg = data.message || 'Terjadi kesalahan.';
                }
            } catch (e) {
                this.errorMsg = 'Gagal menghubungi server.';
            }
            this.submitting = false;
        },

        num(n) { return Number(n).toLocaleString('id-ID'); },
    };
}
</script>
@endsection
