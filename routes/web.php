<?php

use App\Http\Controllers\LaporanController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\TransaksiController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('admin/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'isAdmin'])->name('dashboard');

Route::prefix('admin')->middleware(['auth', 'isAdmin'])->group(function () {
    Route::resource('produks', ProdukController::class);
    Route::resource('pelanggans', PelangganController::class);
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/print', [LaporanController::class, 'print'])->name('laporan.print');
    Route::get('/laporan/print-detail/{id}', [LaporanController::class, 'printDetail'])->name('laporan.print_detail');
    Route::get('/laporan/{id}', [LaporanController::class, 'show'])->name('laporan.show');
});

Route::resource('transaksi', TransaksiController::class)->middleware(['auth']);

// Rute untuk mengelola keranjang transaksi
Route::post('/transaksi/add-to-cart', [TransaksiController::class, 'addToCart'])->name('transaksi.addToCart');
Route::post('/transaksi/update-cart', [TransaksiController::class, 'updateCart'])->name('transaksi.updateCart');
Route::post('/transaksi/remove-from-cart', [TransaksiController::class, 'removeFromCart'])->name('transaksi.removeFromCart');
Route::post('/transaksi/clear-cart', [TransaksiController::class, 'clearCart'])->name('transaksi.clearCart');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
