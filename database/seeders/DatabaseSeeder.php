<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use \Illuminate\Database\Console\Seeds\WithoutModelEvents;

    /**
     * Seed database aplikasi Toko Sembako.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            DataSeeder::class,
        ]);
    }
}
