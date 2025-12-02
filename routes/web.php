<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DatalansiaController;
use App\Http\Controllers\DataperawatController;
use App\Http\Controllers\LaporanController;

Route::get('/', function () {
    return view('auth.login');
});

// Authentication Routes
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->name('logout');
});

// login dan dashboard admin
Route::middleware(['auth'])->group(function () {
Route::get('/admin/dashboard', [DashboardController::class, 'dashboard'])->name('admin.dashboard');

// data lansia (admin)
Route::get('/datalansia', [DatalansiaController::class, 'index'])->name('admin.datalansia.index');
Route::get('/datalansia/tambah', [DatalansiaController::class, 'create'])->name('admin.datalansia.create');
Route::post('/datalansia/store', [DatalansiaController::class, 'store'])->name('admin.datalansia.store');
Route::get('/datalansia/edit/{id}', [DatalansiaController::class, 'edit'])->name('admin.datalansia.edit');
Route::post('/datalansia/update/{id}', [DatalansiaController::class, 'update'])->name('admin.datalansia.update');
Route::get('/datalansia/hapus/{id}', [DatalansiaController::class, 'destroy'])->name('admin.datalansia.destroy');
});

// data perawat (admin)
Route::get('/dataperawat', [DataperawatController::class, 'index'])->name('admin.dataperawat.index');
Route::get('/dataperawat/tambah', [DataperawatController::class, 'create'])->name('admin.dataperawat.create');
Route::post('/dataperawat/store', [DataperawatController::class, 'store'])->name('admin.dataperawat.store');
Route::get('/dataperawat/edit/{id}', [DataperawatController::class, 'edit'])->name('admin.dataperawat.edit');
Route::post('/dataperawat/update/{id}', [DataperawatController::class, 'update'])->name('admin.dataperawat.update');
Route::get('/dataperawat/hapus/{id}', [DataperawatController::class, 'destroy'])->name('admin.dataperawat.destroy');

Route::get('/laporan/pemasukan', [LaporanController::class, 'pemasukan'])->name('laporan.pemasukan');
    Route::post('/laporan/pemasukan/store', [LaporanController::class, 'storePemasukan'])->name('laporan.pemasukan.store');
    Route::delete('/laporan/pemasukan/{id}', [LaporanController::class, 'destroyPemasukan'])->name('laporan.pemasukan.destroy');

    // Laporan Pengeluaran
    Route::get('/laporan/pengeluaran', [LaporanController::class, 'pengeluaran'])->name('laporan.pengeluaran');
    Route::post('/laporan/pengeluaran/store', [LaporanController::class, 'storePengeluaran'])->name('laporan.pengeluaran.store');
    Route::delete('/laporan/pengeluaran/{id}', [LaporanController::class, 'destroyPengeluaran'])->name('laporan.pengeluaran.destroy');


