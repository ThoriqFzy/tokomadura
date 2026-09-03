@extends('layouts.app')
@section('title', 'Kategori')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Kategori Produk</h1>
    <p class="text-sm text-slate-500 mt-0.5">Kelola kategori untuk pengelompokan produk</p>
</div>

@if(session('success'))
<div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm">✅ {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-4 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-lg text-sm">⚠️ {{ session('error') }}</div>
@endif

<div class="grid lg:grid-cols-3 gap-6">
    {{-- Form tambah --}}
    <div class="bg-white rounded-xl border border-slate-200 p-5 h-fit">
        <h2 class="font-semibold text-slate-800 mb-3">+ Tambah Kategori</h2>
        @if($errors->any())
        <div class="mb-3 bg-rose-50 border border-rose-200 text-rose-700 px-3 py-2 rounded-lg text-sm">
            @foreach($errors->all() as $e){{ $e }}<br>@endforeach
        </div>
        @endif
        <form method="POST" action="{{ route('kategori.store') }}" class="space-y-3">
            @csrf
            <div>
                <label class="text-sm font-semibold text-slate-700">Nama Kategori</label>
                <input name="name" type="text" required placeholder="cth: Sembako"
                    class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
            </div>
            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-4 py-2 rounded-lg transition">
                Simpan
            </button>
        </form>
    </div>

    {{-- Daftar kategori --}}
    <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 overflow-hidden h-fit">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 text-left">
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Slug</th>
                    <th class="px-4 py-3 text-center">Jml Produk</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $c)
                <tr class="border-b border-slate-50 hover:bg-slate-50">
                    <td class="px-4 py-3 font-medium">{{ $c->name }}</td>
                    <td class="px-4 py-3 text-slate-400">{{ $c->slug }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-block px-2 py-0.5 rounded-full text-xs bg-slate-100 text-slate-600">{{ $c->products_count }}</span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        {{-- Edit inline via form --}}
                        <button onclick="editKategori('{{ $c->id }}', '{{ $c->name }}')" class="text-emerald-600 hover:underline text-sm">Edit</button>
                        <form method="POST" action="{{ route('kategori.destroy', $c) }}" class="inline" onsubmit="return confirm('Hapus kategori {{ $c->name }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-rose-500 hover:underline text-sm ml-2">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-6 text-center text-slate-400">Belum ada kategori.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal edit --}}
<div id="editModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl p-6 w-full max-w-sm">
        <h2 class="font-semibold text-slate-800 mb-3">Edit Kategori</h2>
        <form id="editForm" method="POST" class="space-y-3">
            @csrf @method('PUT')
            <div>
                <label class="text-sm font-semibold text-slate-700">Nama Kategori</label>
                <input id="editName" name="name" type="text" required
                    class="mt-1 w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-4 py-2 rounded-lg transition">Simpan</button>
                <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-4 py-2 rounded-lg transition">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
function editKategori(id, name) {
    var form = document.getElementById('editForm');
    form.action = '{{ url("kategori") }}/' + id;
    document.getElementById('editName').value = name;
    document.getElementById('editModal').classList.remove('hidden');
}
</script>
@endsection
