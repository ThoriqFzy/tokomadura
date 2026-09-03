<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KasbonController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StokController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => redirect()->route('login'));

/*
|--------------------------------------------------------------------------
| Auth (Breeze) — email & password
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Protected Routes (semua user login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ---- Owner only ----
    Route::middleware('role:owner')->group(function () {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');
        Route::resource('produk', ProdukController::class)->except(['show']);
        Route::resource('kategori', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('stok', StokController::class)->only(['index', 'store']);
        Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
    });

    // ---- Owner & Kasir ----
    Route::get('pos', [PosController::class, 'index'])->name('pos.index');
    Route::get('kasbon', [KasbonController::class, 'index'])->name('kasbon.index');
});
