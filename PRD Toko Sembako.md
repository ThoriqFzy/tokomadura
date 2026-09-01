# PRD: Sistem Toko Sembako (Web App)

**Owner:** Thoriq Fauzi
**Stack:** Laravel + MySQL
**Status:** Draft v1
**Tipe proyek:** Real business tool (dipakai sendiri/keluarga) — bukan sekadar latihan

---

## 1. Problem & Tujuan

Toko sembako yang kamu/keluarga kelola sekarang jalan manual — catatan stok dan transaksi kemungkinan besar masih di kertas/buku atau nggak konsisten. Itu artinya:

- Stok bisa selisih tanpa ketahuan (barang hilang, salah hitung, lupa catat)
- Nggak ada data buat ambil keputusan (produk apa yang laku, kapan harus restock, margin per produk)
- Waktu kasir habis buat hitung manual, bukan buat layani pelanggan

**Tujuan sistem ini:** ganti proses manual itu jadi satu sistem yang mencatat semua transaksi, stok, dan laporan secara otomatis — real-time, akurat, dan bisa diakses kapan aja.

Ini juga sekaligus jadi *proof of work* buat portofolio backend dev kamu. Tapi itu bonus — prioritas #1 tetap: sistem ini harus **beneran jalan** buat operasional toko sehari-hari. Kalau cuma bagus di GitHub tapi ribet dipakai kasir di lapangan, PRD ini gagal.

---

## 2. User & Role

| Role | Akses |
|---|---|
| **Owner/Admin** (kamu) | Full access — kelola produk, harga, stok, user, lihat semua laporan, hapus/edit data |
| **Kasir/Karyawan** | Input transaksi jual, lihat stok (read-only), cetak struk. **Tidak bisa** ubah harga, hapus transaksi, atau lihat laporan keuangan sensitif |

Kenapa dipisah begini: kasir yang megang uang langsung nggak butuh (dan nggak boleh) akses ubah harga atau hapus histori transaksi. Ini bukan soal nggak percaya — ini soal audit trail. Kalau ada selisih, kamu tau siapa yang ngapain.

---

## 3. Scope

### MVP (harus ada, ini yang dipakai duluan)
1. Auth + role management (login owner vs kasir)
2. Manajemen produk & kategori (CRUD)
3. Manajemen stok (stok masuk/keluar, alert stok minim)
4. POS/Kasir — transaksi jual, multi metode bayar (cash/QRIS/utang), hitung kembalian, cetak struk ke printer thermal existing
5. Sistem utang/bon (kasbon) pelanggan langganan
6. Laporan penjualan harian & bulanan (termasuk piutang beredar)
7. Dashboard ringkas (omzet hari ini, produk terlaris, stok kritis, total kasbon berjalan)

**Catatan revisi:** printer thermal awalnya gue taruh di Phase 2, tapi karena toko udah punya hardware-nya, nggak ada alasan pakai `window.print()` sebagai workaround sementara — langsung ESC/POS di MVP. Ini contoh kenapa scope harus fleksibel ngikutin kondisi real, bukan template baku.

### Phase 2 (setelah MVP jalan stabil di toko, jangan dikerjain bareng MVP)
- Katalog online publik (pelanggan bisa cek harga/stok dari HP sebelum ke toko)
- Notifikasi WA/Telegram otomatis kalau stok menipis ATAU kasbon pelanggan udah lewat batas — ini bisa **nyambung langsung ke workflow n8n** yang udah kamu punya, tinggal extend, bukan bikin dari nol. High leverage.
- Manajemen supplier & purchase order
- Integrasi payment gateway QRIS dinamis (generate QR otomatis per transaksi) — MVP cukup QRIS statis + konfirmasi manual kasir
- Multi-cabang (kalau toko berkembang)

**Kenapa dipisah tegas begini:** kalau semua fitur digarap bareng, kamu nggak akan pernah selesai MVP. Toko butuh sistem yang jalan minggu depan, bukan sistem sempurna 6 bulan lagi. Ship MVP, pakai di toko real, baru iterate berdasarkan apa yang beneran kurang — bukan asumsi.

---

## 4. Functional Requirements

### 4.1 Auth & Role Management
- Login dengan email/username + password
- Role: `owner`, `kasir` (pakai package **Spatie Laravel-Permission**, jangan bikin sistem role manual dari nol — itu effort manual yang nggak perlu, sudah ada solusi matang)
- Middleware proteksi per-route sesuai role

### 4.2 Manajemen Produk & Kategori
- CRUD produk: nama, kategori, harga beli, harga jual, satuan (kg/pcs/liter/dus), stok saat ini
- CRUD kategori (sembako, minuman, snack, dll)
- Search & filter produk by nama/kategori

### 4.3 Manajemen Stok
- Input stok masuk (restock) dengan catatan tanggal & jumlah
- Stok otomatis berkurang saat ada transaksi jual
- Alert/highlight kalau stok di bawah ambang minimum (misal < 5 unit)
- Riwayat pergerakan stok (stock movement log) — penting buat audit kalau ada selisih

### 4.4 POS / Transaksi Kasir
- Interface kasir: cari produk cepat by nama/kode dengan live search/autocomplete — ini krusial karena ratusan SKU tanpa barcode, kasir nggak boleh scroll-scroll manual
- Tambah ke keranjang, hitung total otomatis
- Pilih metode bayar per transaksi: **Cash**, **QRIS/Transfer**, atau **Utang** (pilih pelanggan terdaftar)
  - Cash → input uang diterima, sistem hitung kembalian otomatis
  - QRIS/Transfer (MVP) → pakai QRIS statis toko, kasir konfirmasi manual setelah lihat notifikasi masuk. Integrasi API generate-QR-per-transaksi itu Phase 2, kompleksitasnya nggak worth it buat 1 kasir
  - Utang → nominal masuk sebagai piutang ke akun pelanggan tsb, transaksi tetap tercatat lunas dari sisi stok (barang keluar), tapi belum lunas dari sisi kas
- Simpan transaksi + detail item yang dibeli
- Cetak struk ke printer thermal Eppos via Bluetooth (integrasi ESC/POS) — lihat catatan arsitektur printing di bagian Risiko
- Transaksi tersimpan real-time, stok langsung ke-update

### 4.5 Utang/Bon Pelanggan (Kasbon)
- Data pelanggan langganan: nama, no HP, saldo utang berjalan
- Transaksi dengan metode "utang" otomatis nambah saldo utang pelanggan tsb
- Riwayat pelunasan: pelanggan bisa bayar cicil atau lunas kapan aja, tiap pembayaran tercatat (tanggal, jumlah, siapa yang terima)
- **Kontrol akses:** kasir cuma bisa buat transaksi utang baru & catat pelunasan. Hapus/waive utang cuma bisa owner — ini audit control, biar kasbon nggak "dihapus" sembarangan
- Laporan piutang beredar: siapa aja yang masih punya utang & berapa totalnya

### 4.6 Laporan & Dashboard
- Laporan penjualan harian, mingguan, bulanan (filter by tanggal, breakdown per metode bayar)
- Produk terlaris & paling lambat terjual
- Omzet vs modal (kalau harga beli diisi konsisten, bisa hitung margin kasar)
- Laporan piutang/kasbon beredar
- Dashboard owner: ringkasan angka penting tanpa harus buka laporan detail

### 4.7 [Phase 2] Katalog Online
- Halaman publik nampilin produk + harga (read-only, no transaksi online dulu)
- Update otomatis ngikutin data stok/harga dari sistem utama

---

## 5. Non-Functional Requirements

- **Performance:** transaksi kasir harus responsif (<1 detik) — pelanggan ngantri nggak boleh nunggu loading
- **Security:** password di-hash (Laravel default bcrypt), validasi input ketat (Laravel udah handle SQL injection by default via Eloquent/query builder, tapi tetap validasi form input), **backup database otomatis harian** — ini toko real, data transaksi hilang = kerugian nyata, bukan bug yang bisa di-ignore
- **Usability:** UI kasir harus minim klik. Karyawan toko sembako biasanya bukan orang tech-savvy — kalau UI ribet, mereka bakal balik ke nota manual
- **Reliability:** sistem harus tetap bisa jalan walau koneksi internet lemot (pertimbangkan local server/hosting kalau lokasi toko susah sinyal — cek dulu kondisi realnya)

---

## 6. Tech Stack (rekomendasi + alasan)

| Layer | Pilihan | Kenapa |
|---|---|---|
| Backend | Laravel 11 | Sesuai request kamu, ekosistem matang, banyak referensi belajar |
| Database | MySQL | Sesuai request, cocok untuk data transaksional relational kayak POS |
| Auth & Role | Laravel Breeze + Spatie Laravel-Permission | Jangan reinvent the wheel — role-based access udah solved problem |
| Frontend | Blade + **Livewire** | Kamu lagi belajar Laravel dari dasar. Livewire = reaktif UI (kayak transaksi kasir yang butuh update real-time) tanpa harus belajar stack terpisah (React/Vue). Ini **compounding learning** — skill PHP/Blade kamu langsung kepake, bukan effort ganda |
| Styling | Tailwind CSS | Cepat, nggak perlu nulis CSS manual dari nol |
| Print struk | Eppos thermal printer (Bluetooth) via **Web Bluetooth API**, ESC/POS | Device kasir Android + Chrome = support penuh. Zero extra component. Wajib HTTPS |

**Kenapa nggak langsung ke Vue/React SPA + API terpisah:** karena itu menambah kompleksitas (butuh belajar 2 ekosistem sekaligus) tanpa menambah value buat toko real di tahap ini. Leverage kamu sekarang ada di "selesaikan MVP secepat mungkin dengan tools yang udah kamu kuasai arahnya," bukan di "pakai stack paling canggih."

---

## 7. Database Schema (garis besar)

```
users (id, name, email, password, role)
categories (id, name)
products (id, category_id, name, unit, buy_price, sell_price, stock, min_stock)
stock_movements (id, product_id, type[in/out], qty, note, created_by, created_at)
customers (id, name, phone, debt_balance)
transactions (id, cashier_id, customer_id[nullable], payment_method[cash/qris/debt], total_amount, paid_amount, change_amount, created_at)
transaction_items (id, transaction_id, product_id, qty, price_at_sale, subtotal)
debt_payments (id, customer_id, amount, paid_at, recorded_by)
```

`price_at_sale` penting — kalau harga produk berubah di masa depan, histori transaksi lama harus tetap nunjukin harga waktu itu, bukan harga sekarang.

`customer_id` di transactions cuma diisi kalau `payment_method = debt` — transaksi cash/QRIS nggak perlu data pelanggan.

---

## 8. Roadmap Development

| Fase | Isi | Fokus |
|---|---|---|
| 0 | Setup project, auth, role, layout dasar | Fondasi |
| 1 | Manajemen produk & kategori | CRUD dasar |
| 2 | Manajemen stok + stock movement log | Data akurat |
| 3 | POS/Kasir (transaksi + struk) | Core value |
| 4 | Laporan & dashboard | Insight |
| 5 (opsional) | Katalog online + notifikasi WA/n8n | Scale |

Kerjain fase 0-4 dulu sampai bisa dipakai transaksi beneran di toko. Fase 5 nunggu MVP terbukti stabil minimal 2-4 minggu pemakaian real.

---

## 9. Success Metrics

- Semua transaksi tercatat tanpa selisih manual (real-world usefulness — ini alasan sistem ini dibikin)
- Owner bisa tarik laporan penjualan dalam < 1 menit, bukan hitung manual berjam-jam
- Stok tercatat akurat, alert stok minim kepake buat restock tepat waktu
- Sistem ini jadi studi kasus konkret di portofolio kamu — real business, real data, real problem solved (authority — bukan project tutorial doang)

---

## 10. Risks & Assumptions

- **Asumsi:** kasir bukan orang tech-savvy → UI harus dijaga sesederhana mungkin, uji langsung ke orang yang bakal pakai, bukan cuma ke kamu sendiri
- **Risk:** tanpa barcode scanner, input manual di kasir bisa lambat pas jam ramai — pertimbangkan search produk by nama/kode singkat sebagai mitigasi MVP
- **Risk terbesar:** kamu solo dev + kerja shift + kuliah. Kalau scope nggak dijaga ketat ke MVP, project ini bisa stuck di "hampir jadi" selamanya. Disiplin ke roadmap di atas, bukan nambah fitur baru di tengah jalan.
- **Asumsi teknis:** koneksi internet di lokasi toko perlu dicek — kalau nggak stabil, arsitektur hosting perlu disesuaikan (local network vs cloud)
- **Keputusan printing — FINAL:** device kasir Android → pakai **Web Bluetooth API** (Chrome Android support penuh). Nggak perlu print bridge app terpisah. Browser langsung connect ke printer Eppos via Bluetooth, kirim data ESC/POS, selesai. Ini leverage tertinggi: zero komponen tambahan, zero maintenance ekstra, langsung native di web app Laravel kamu. Satu syarat teknis: aplikasi wajib diakses via HTTPS (Web Bluetooth API nolak jalan di HTTP biasa) — pastikan ini masuk requirement hosting/deployment.

---

## Status: PRD Final

Semua keputusan requirement & arsitektur udah lengkap:

- Ratusan SKU → live search by nama/kode di kasir (bukan barcode)
- 1 kasir, device Android → printing pakai Web Bluetooth API langsung, zero extra component
- Kasbon wajib, dengan audit control (kasir input, owner hapus)
- Cash + QRIS statis, tanpa integrasi payment gateway
- Hosting wajib HTTPS (syarat Web Bluetooth API)

Nggak ada lagi pertanyaan blocking. Gas mulai **Fase 0**: setup project Laravel, auth, role management (Spatie), base layout. Itu fondasi yang semua modul lain bakal nempel di atasnya — jangan lompat ke POS dulu sebelum ini beres.
