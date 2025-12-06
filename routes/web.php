<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DatalansiaController;
use App\Http\Controllers\DataPerawatController;
use App\Http\Controllers\GrafikController;

Route::get('/', function () {
    return view('auth.login');
});

// Authentication Routes
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->name('logout');
});

// Routes dengan middleware auth
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/admin/dashboard', [DashboardController::class, 'dashboard'])->name('admin.dashboard');
    
    // Laporan Dashboard
    Route::get('/laporan/dashboard', [LaporanController::class, 'dashboard'])->name('laporan.dashboard');
    
    // Laporan Pemasukan
    Route::get('/laporan/pemasukan', [LaporanController::class, 'pemasukan'])->name('laporan.pemasukan');
    Route::post('/laporan/pemasukan/store', [LaporanController::class, 'storePemasukan'])->name('laporan.pemasukan.store');
    Route::get('/laporan/pemasukan/edit/{id}', [LaporanController::class, 'editPemasukan'])->name('laporan.pemasukan.edit');
    Route::post('/laporan/pemasukan/update/{id}', [LaporanController::class, 'updatePemasukan'])->name('laporan.pemasukan.update');
    Route::delete('/laporan/pemasukan/{id}', [LaporanController::class, 'destroyPemasukan'])->name('laporan.pemasukan.destroy');
    
    // Laporan Pengeluaran
    Route::get('/laporan/pengeluaran', [LaporanController::class, 'pengeluaran'])->name('laporan.pengeluaran');
    Route::post('/laporan/pengeluaran/store', [LaporanController::class, 'storePengeluaran'])->name('laporan.pengeluaran.store');
    Route::get('/laporan/pengeluaran/edit/{id}', [LaporanController::class, 'editPengeluaran'])->name('laporan.pengeluaran.edit');
    Route::post('/laporan/pengeluaran/update/{id}', [LaporanController::class, 'updatePengeluaran'])->name('laporan.pengeluaran.update');
    Route::delete('/laporan/pengeluaran/{id}', [LaporanController::class, 'destroyPengeluaran'])->name('laporan.pengeluaran.destroy');
    
    // Export Routes
    Route::get('/laporan/pemasukan/export-pdf', [LaporanController::class, 'exportPemasukanPdf'])->name('laporan.pemasukan.export.pdf');
    Route::get('/laporan/pengeluaran/export-pdf', [LaporanController::class, 'exportPengeluaranPdf'])->name('laporan.pengeluaran.export.pdf');
    
    // Data Lansia (admin)
    Route::get('/datalansia', [DatalansiaController::class, 'index'])->name('admin.datalansia.index');
    Route::get('/datalansia/tambah', [DatalansiaController::class, 'create'])->name('admin.datalansia.create');
    Route::post('/datalansia/store', [DatalansiaController::class, 'store'])->name('admin.datalansia.store');
    Route::get('/datalansia/edit/{id}', [DatalansiaController::class, 'edit'])->name('admin.datalansia.edit');
    Route::post('/datalansia/update/{id}', [DatalansiaController::class, 'update'])->name('admin.datalansia.update');
    Route::get('/datalansia/hapus/{id}', [DatalansiaController::class, 'destroy'])->name('admin.datalansia.destroy');
    
    // Data Perawat (admin)
    Route::get('/DataPerawat', [DataPerawatController::class, 'index'])->name('admin.DataPerawat.index');
    Route::get('/DataPerawat/tambah', [DataPerawatController::class, 'create'])->name('admin.DataPerawat.create');
    Route::post('/DataPerawat/store', [DataPerawatController::class, 'store'])->name('admin.DataPerawat.store');
    Route::get('/DataPerawat/edit/{id}', [DataPerawatController::class, 'edit'])->name('admin.DataPerawat.edit');
    Route::post('/DataPerawat/update/{id}', [DataPerawatController::class, 'update'])->name('admin.DataPerawat.update');
    Route::get('/DataPerawat/hapus/{id}', [DataPerawatController::class, 'destroy'])->name('admin.DataPerawat.destroy');

    // Grafik
    Route::get('/grafik', [GrafikController::class, 'index'])->name('admin.grafik.index');
    Route::get('/grafik/kategori', [GrafikController::class, 'byCategory'])->name('admin.grafik.category');
    Route::get('/dashboard', [GrafikController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/api/grafik-data', [GrafikController::class, 'apiData'])->name('admin.grafik.api');
});