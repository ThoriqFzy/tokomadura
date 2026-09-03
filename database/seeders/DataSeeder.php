<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DataSeeder extends Seeder
{
    /**
     * Seed data awal: user dummy, kategori, produk contoh, pelanggan contoh.
     * NOTA: user dummy ini untuk development/testing — ganti/amankan sebelum produksi.
     */
    public function run(): void
    {
        // --- Users ---
        $owner = User::updateOrCreate(
            ['email' => 'admin@toko.id'],
            ['name' => 'Thoriq (Owner)', 'password' => 'admin123']
        );
        $kasir = User::updateOrCreate(
            ['email' => 'kasir@toko.id'],
            ['name' => 'Dina (Kasir)', 'password' => 'kasir123']
        );

        $owner->syncRoles(['owner']);
        $kasir->syncRoles(['kasir']);

        $this->command?->info('Users & roles siap: admin@toko.id / kasir@toko.id');

        // --- Categories ---
        $cats = [
            ['name' => 'Sembako', 'slug' => 'sembako'],
            ['name' => 'Minuman', 'slug' => 'minuman'],
            ['name' => 'Snack', 'slug' => 'snack'],
            ['name' => 'Rumah Tangga', 'slug' => 'rumah-tangga'],
        ];
        foreach ($cats as $c) {
            Category::updateOrCreate(['slug' => $c['slug']], $c);
        }

        // --- Products (contoh, dengan SKU) ---
        $products = [
            ['sku' => 'BR5', 'name' => 'Beras Premium 5kg', 'cat' => 'sembako', 'unit' => 'pcs', 'buy' => 62000, 'sell' => 68000, 'stock' => 42, 'min' => 5],
            ['sku' => 'MG1', 'name' => 'Minyak Goreng 1L', 'cat' => 'sembako', 'unit' => 'pcs', 'buy' => 17500, 'sell' => 20000, 'stock' => 3, 'min' => 5],
            ['sku' => 'GP1', 'name' => 'Gula Pasir 1kg', 'cat' => 'sembako', 'unit' => 'pcs', 'buy' => 16000, 'sell' => 18000, 'stock' => 2, 'min' => 5],
            ['sku' => 'TY', 'name' => 'Telur Ayam', 'cat' => 'sembako', 'unit' => 'kg', 'buy' => 24000, 'sell' => 28000, 'stock' => 4, 'min' => 5],
            ['sku' => 'KS', 'name' => 'Kopi Sachet', 'cat' => 'minuman', 'unit' => 'dus', 'buy' => 21000, 'sell' => 24000, 'stock' => 15, 'min' => 3],
            ['sku' => 'MIE', 'name' => 'Mie Instan Goreng', 'cat' => 'snack', 'unit' => 'dus', 'buy' => 96000, 'sell' => 110000, 'stock' => 1, 'min' => 3],
            ['sku' => 'AM6', 'name' => 'Air Mineral 600ml', 'cat' => 'minuman', 'unit' => 'pcs', 'buy' => 3000, 'sell' => 4000, 'stock' => 60, 'min' => 12],
            ['sku' => 'TB', 'name' => 'Teh Botol', 'cat' => 'minuman', 'unit' => 'pcs', 'buy' => 4500, 'sell' => 6000, 'stock' => 48, 'min' => 12],
            ['sku' => 'SC', 'name' => 'Sabun Cair', 'cat' => 'rumah-tangga', 'unit' => 'pcs', 'buy' => 14000, 'sell' => 18000, 'stock' => 9, 'min' => 3],
            ['sku' => 'GB2', 'name' => 'Garam 250gr', 'cat' => 'sembako', 'unit' => 'pcs', 'buy' => 3500, 'sell' => 5000, 'stock' => 20, 'min' => 5],
            ['sku' => 'TEP', 'name' => 'Tepung Terigu 1kg', 'cat' => 'sembako', 'unit' => 'pcs', 'buy' => 9500, 'sell' => 12000, 'stock' => 14, 'min' => 5],
            ['sku' => 'KEC', 'name' => 'Kecap Botol 600ml', 'cat' => 'rumah-tangga', 'unit' => 'pcs', 'buy' => 12000, 'sell' => 15000, 'stock' => 6, 'min' => 3],
        ];
        foreach ($products as $p) {
            $cat = Category::where('slug', $p['cat'])->first();
            Product::updateOrCreate(
                ['sku' => $p['sku']],
                [
                    'category_id' => $cat?->id,
                    'name' => $p['name'],
                    'unit' => $p['unit'],
                    'buy_price' => $p['buy'],
                    'sell_price' => $p['sell'],
                    'stock' => $p['stock'],
                    'min_stock' => $p['min'],
                    'is_active' => true,
                ]
            );
        }

        $this->command?->info(count($products) . ' produk contoh dibuat.');

        // --- Customers contoh ---
        $customers = [
            ['name' => 'Mas Budi', 'phone' => '0812-1111-0001'],
            ['name' => 'Bu Sari', 'phone' => '0812-1111-0002'],
            ['name' => 'Pak Darmo', 'phone' => '0812-1111-0003'],
            ['name' => 'Bu Ani', 'phone' => '0812-1111-0004'],
        ];
        foreach ($customers as $c) {
            Customer::updateOrCreate(['phone' => $c['phone']], $c);
        }

        $this->command?->info(count($customers) . ' pelanggan contoh dibuat.');
    }
}
