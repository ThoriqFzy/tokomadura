<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $cat = fn(string $slug) => Category::where('slug', $slug)->first()->id;

        $products = [
            // === Mie Instan (Sembako) ===
            ['Mie Sedaap Soto',         'mie-sedaap-soto',         'sembako', 200, 2500, 3000,  48, 10, 'pcs'],
            ['Mie Sedaap Goreng',       'mie-sedaap-goreng',       'sembako', 200, 2500, 3000,  36, 10, 'pcs'],
            ['Mie Sedaap Kari Ayam',    'mie-sedaap-kari',         'sembako', 200, 2500, 3000,  24, 10, 'pcs'],
            ['Mie Sedaap Ayam Bawang',  'mie-sedaap-ayam-bawang',  'sembako', 200, 2500, 3000,  18, 10, 'pcs'],
            ['Indomie Goreng',          'indomie-goreng',          'sembako', 220, 3000, 3500,  40, 10, 'pcs'],
            ['Indomie Soto',            'indomie-soto',            'sembako', 220, 3000, 3500,  30, 10, 'pcs'],
            ['Supermi Goreng',          'supermi-goreng',          'sembako', 210, 2500, 3000,  20, 10, 'pcs'],

            // === Minyak Goreng (Sembako) ===
            ['Minyak Bimoli 1L',        'minyak-bimoli',           'sembako', 18500, 20000, 22000, 20, 5, 'pcs'],
            ['Minyak Fortune 1L',       'minyak-fortune',          'sembako', 18000, 19500, 21000, 15, 5, 'pcs'],
            ['Minyak Sunco 1L',         'minyak-sunco',            'sembako', 18000, 19500, 21000, 12, 5, 'pcs'],
            ['Minyak Sania 1L',         'minyak-sania',            'sembako', 17000, 18500, 20000, 10, 5, 'pcs'],
            ['Minyak Filma 1L',         'minyak-filma',            'sembako', 17800, 19000, 20000,  8, 5, 'pcs'],

            // === Beras (Sembako) ===
            ['Beras Premium 5kg',       'beras-premium-5kg',       'sembako', 62000, 68000, 70000, 20, 5, 'sak'],
            ['Beras Medium 5kg',        'beras-medium-5kg',        'sembako', 58000, 64000, 65000, 12, 5, 'sak'],
            ['Beras Merah 1kg',         'beras-merah-1kg',         'sembako', 14000, 16000, 17000, 15, 5, 'pack'],
            ['Beras Ketan 1kg',         'beras-ketan-1kg',         'sembako', 15000, 17000, 18000, 10, 5, 'pack'],

            // === Gula & Bumbu (Sembako) ===
            ['Gula Pasir 1kg',          'gula-pasir-1kg',          'sembako', 15500, 17000, 18000, 25, 5, 'pack'],
            ['Gula Merah 1/2kg',        'gula-merah',              'sembako', 12000, 13500, 14000, 18, 5, 'pack'],
            ['Tepung Terigu Segitiga 1kg','tepung-segitiga-1kg',   'sembako', 11500, 13000, 13500, 22, 5, 'pack'],
            ['Tepung Bola Salju 1kg',   'tepung-bola-salju-1kg',   'sembako', 12500, 14000, 14500, 14, 5, 'pack'],
            ['Telur Ayam 1kg',          'telur-ayam-1kg',          'sembako', 28000, 30000, 32000, 30, 5, 'pack'],
            ['Kecap ABC 275ml',         'kecap-abc',               'sembako', 8000,  9500,  10000, 30, 5, 'botol'],
            ['Saus Sambal ABC 135ml',   'saus-sambal-abc',         'sembako', 6000,  7000,  8000,  25, 5, 'botol'],
            ['Minyak Kelapa 250ml',     'minyak-kelapa',           'sembako', 13000, 15000, 16000, 15, 5, 'pack'],

            // === Minuman (Minuman) ===
            ['Air Mineral 600ml',       'air-mineral-600',         'minuman', 3000,  4000,  5000,  50, 10, 'botol'],
            ['Teh Botol Sosro 350ml',   'teh-botol-sosro',         'minuman', 3500,  4500,  5000,  40, 10, 'botol'],
            ['Kopi Kapal Api 1sachet',  'kopi-kapal-api',          'minuman', 1500,  2000,  2500,  100, 20, 'sachet'],
            ['Susu Ultra 250ml',        'susu-ultra-250',          'minuman', 5000,  6500,  7000,  24, 10, 'kotak'],
            ['Susu Ultra 1L',           'susu-ultra-1l',           'minuman', 17000, 19500, 20000, 12, 5,  'kotak'],

            // === Snack (Snack) ===
            ['Keripik Singkong',        'keripik-singkong',        'snack',   6000,  7500,  8000,  20, 5, 'pack'],
            ['Kacang Garuda 250g',      'kacang-garuda',           'snack',   13000, 15000, 16000, 15, 5, 'pack'],
            ['Biskuit Roma Kelapa 300g','roma-kelapa',             'snack',   10000, 12000, 13000, 18, 5, 'pack'],
            ['Wafer Nabati Keju',       'nabati-keju',             'snack',   9000,  10500, 11000, 16, 5, 'pack'],
            ['Ciki Mie Sapi Panggang',  'ciki-mie',                'snack',   1000,  1500,  2000,  60, 10, 'pcs'],
            ['Chocolatos Wafer',        'chocolatos',              'snack',   2000,  2500,  3000,  40, 10, 'pcs'],

            // === Rumah Tangga (Rumah Tangga) ===
            ['Shampo Sachet',           'shampo-sachet',           'rumah-tangga', 500,  1000,  1500, 80, 20, 'sachet'],
            ['Sabun Mandi Lifebuoy 80g','sabun-lifebuoy',          'rumah-tangga', 3500, 4500,  5000, 30, 5,  'pcs'],
            ['Sabun Cuci Piring 180ml','sabun-cuci-piring',        'rumah-tangga', 5000, 6000,  7000, 20, 5,  'botol'],
            ['Deterjen Rinso 500g',     'deterjen-rinso',          'rumah-tangga', 15000, 17000, 18000, 15, 5, 'pack'],
        ];

        // Hapus produk lama (yang dari fase awal) agar konsisten — TAPI jangan jika sudah ada transaksi
        // Pilih: tambah saja produk baru (update yg sudah ada)
        foreach ($products as [$name, $slug, $catSlug, $buy, $sell, $min, $stock, $minStock, $unit]) {
            $sku = strtoupper(substr(str_replace(['-', ' '], '', $slug), 0, 10)) . rand(100, 999);

            Product::updateOrCreate(
                ['name' => $name],
                [
                    'category_id' => $cat($catSlug),
                    'sku'         => $sku,
                    'image'       => "products/{$slug}.jpg",
                    'unit'        => $unit,
                    'buy_price'   => $buy,
                    'sell_price'  => $sell,
                    'stock'       => $stock,
                    'min_stock'   => $minStock,
                    'is_active'   => true,
                ]
            );
        }

        $this->command->info('Seeder produk demo selesai: ' . count($products) . ' produk.');
    }
}
