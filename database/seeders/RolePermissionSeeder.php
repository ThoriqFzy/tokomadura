<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Buat 2 role: owner & kasir.
     * Owner = akses penuh. Kasir = terbatas (transaksi jual + kasbon input).
     */
    public function run(): void
    {
        // Guard default (web) — role owner punya semua permission via Gate, jadi
        // kita cukup pastikan role owner ada. Kasir bisa diberi permission spesifik
        // saat modul POS dibuat (Fase 3). Di sini cukup role dasar.
        Role::findOrCreate('owner', 'web');
        Role::findOrCreate('kasir', 'web');

        $this->command?->info('Roles owner & kasir siap.');
    }
}
