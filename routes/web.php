<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\IuranController;
use App\Http\Controllers\DonasiController;
use App\Http\Controllers\GrafikController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DatalansiaController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\KampanyeDonasiController;
use App\Http\Controllers\DataPerawatController;

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
    
    // Profile routes
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
        Route::get('/password/edit', [ProfileController::class, 'editPassword'])->name('edit-password');
        Route::put('/password/update', [ProfileController::class, 'updatePassword'])->name('update-password');
        Route::delete('/photo/delete', [ProfileController::class, 'deletePhoto'])->name('delete-photo');
        Route::post('/photo/upload', [ProfileController::class, 'uploadPhoto'])->name('upload-photo');
    });
    
    // Laporan Dashboard
    Route::get('/laporan/dashboard', [LaporanController::class, 'dashboard'])->name('laporan.dashboard');
    
    // Laporan Pemasukan
    Route::put('/laporan/pemasukan/{id}', [LaporanController::class, 'updatePemasukan'])->name('laporan.pemasukan.update');
    Route::delete('/laporan/pemasukan/{id}', [LaporanController::class, 'destroyPemasukan'])->name('laporan.pemasukan.destroy');
    Route::get('/laporan/pemasukan', [LaporanController::class, 'pemasukan'])->name('laporan.pemasukan');
    Route::post('/laporan/pemasukan/store', [LaporanController::class, 'storePemasukan'])->name('laporan.pemasukan.store');
    Route::get('/laporan/pemasukan/edit/{id}', [LaporanController::class, 'editPemasukan'])->name('laporan.pemasukan.edit');
    
    // Laporan Pengeluaran
    Route::get('/laporan/pengeluaran', [LaporanController::class, 'pengeluaran'])->name('laporan.pengeluaran');
    Route::post('/laporan/pengeluaran/store', [LaporanController::class, 'storePengeluaran'])->name('laporan.pengeluaran.store');
    Route::get('/laporan/pemasukan/{id}/edit', [LaporanController::class, 'getPemasukanForEdit'])->name('laporan.pemasukan.edit.json');
    Route::get('/laporan/pengeluaran/{id}/edit', [LaporanController::class, 'getPengeluaranForEdit'])->name('laporan.pengeluaran.edit.json');
    Route::put('/laporan/pengeluaran/{id}', [LaporanController::class, 'updatePengeluaran'])->name('laporan.pengeluaran.update');
    Route::delete('/laporan/pengeluaran/{id}', [LaporanController::class, 'destroyPengeluaran'])->name('laporan.pengeluaran.destroy');
    
    // Export Routes Laporan
    Route::get('/laporan/pemasukan/export-pdf', [LaporanController::class, 'exportPemasukanPdf'])->name('laporan.pemasukan.export.pdf');
    Route::get('/laporan/pengeluaran/export-pdf', [LaporanController::class, 'exportPengeluaranPdf'])->name('laporan.pengeluaran.export.pdf');
    
    // ===========================================
    // DATA LANSIA ROUTES (PERBAIKAN)
    // ===========================================
    Route::prefix('datalansia')->name('admin.datalansia.')->group(function () {
        Route::get('/', [DatalansiaController::class, 'index'])->name('index');
        Route::get('/create', [DatalansiaController::class, 'create'])->name('create');
        Route::post('/', [DatalansiaController::class, 'store'])->name('store');
        Route::get('/{id}', [DatalansiaController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [DatalansiaController::class, 'edit'])->name('edit');
        Route::put('/{id}', [DatalansiaController::class, 'update'])->name('update');
        Route::delete('/{id}', [DatalansiaController::class, 'destroy'])->name('destroy');
         Route::get('/datalansia/export', [DatalansiaController::class, 'export'])->name('export');

    }); 
    
    // ===========================================
    // DATA PERAWAT ROUTES (SAMA DENGAN LANSIA)
    // ===========================================
    Route::prefix('DataPerawat')->name('admin.DataPerawat.')->group(function () {
        Route::get('/', [DataPerawatController::class, 'index'])->name('index');
        Route::get('/create', [DataPerawatController::class, 'create'])->name('create');
        Route::post('/', [DataPerawatController::class, 'store'])->name('store');
        Route::get('/{id}', [DataPerawatController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [DataPerawatController::class, 'edit'])->name('edit');
        Route::put('/{id}', [DataPerawatController::class, 'update'])->name('update');
        Route::delete('/{id}', [DataPerawatController::class, 'destroy'])->name('destroy');
    });
    
    // Donasi Routes
    Route::prefix('donasi')->name('admin.donasi.')->group(function () {
        Route::get('/', [DonasiController::class, 'index'])->name('index');
        Route::get('/export', [DonasiController::class, 'export'])->name('export');
        Route::post('/export-filtered', [DonasiController::class, 'exportFiltered'])->name('export-filtered');
        Route::get('/export-summary', [DonasiController::class, 'exportSummary'])->name('export-summary');
        Route::get('/check-pending', [DonasiController::class, 'checkPending'])->name('check-pending');
        Route::get('/{donasi}', [DonasiController::class, 'show'])
            ->where('donasi', '[0-9]+')
            ->name('show');
        Route::post('/{donasi}/status', [DonasiController::class, 'updateStatus'])->name('updateStatus');
    });
    
    // Kampanye Donasi
    Route::prefix('kampanye')->name('admin.kampanye.')->group(function () {
        Route::get('/', [KampanyeDonasiController::class, 'index'])->name('index');
        Route::get('/create', [KampanyeDonasiController::class, 'create'])->name('create');
        Route::post('/', [KampanyeDonasiController::class, 'store'])->name('store');
        Route::get('/{kampanye}/edit', [KampanyeDonasiController::class, 'edit'])->name('edit');
        Route::put('/{kampanye}', [KampanyeDonasiController::class, 'update'])->name('update');
        Route::delete('/{kampanye}', [KampanyeDonasiController::class, 'destroy'])->name('destroy');
        Route::get('/{id}', [KampanyeDonasiController::class, 'show'])->name('show');
        Route::post('/{kampanye}/status', [KampanyeDonasiController::class, 'updateStatus'])->name('updateStatus');
        Route::get('/export/excel', [KampanyeDonasiController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export/pdf', [KampanyeDonasiController::class, 'exportPdf'])->name('export.pdf');
    });
    
    // Iuran Routes
    Route::prefix('iuran')->name('admin.iuran.')->group(function () {
        Route::get('/', [IuranController::class, 'index'])->name('index');
        Route::get('/create', [IuranController::class, 'create'])->name('create');
        Route::post('/', [IuranController::class, 'store'])->name('store');
        Route::post('/generate', [IuranController::class, 'generateFromTemplate'])->name('generate');
        Route::post('/bulk-generate', [IuranController::class, 'bulkGenerate'])->name('bulk-generate');
        Route::post('/{iuran}/mark-paid', [IuranController::class, 'markAsPaid'])->name('mark-paid');
        Route::post('/{iuran}/status', [IuranController::class, 'updateStatus'])->name('update-status');
        Route::delete('/{iuran}', [IuranController::class, 'destroy'])->name('destroy');
        Route::get('/check-late', [IuranController::class, 'checkLateIuran'])->name('check-late');
        Route::get('/export', [IuranController::class, 'export'])->name('export');
        Route::get('/{iuran}/detail', [IuranController::class, 'detail'])->name('detail');
        Route::get('/statistics', [IuranController::class, 'statistics'])->name('statistics');
    });
    
    // Notifications Routes
    Route::prefix('notifications')->name('admin.notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/create', [NotificationController::class, 'create'])->name('create');
        Route::post('/', [NotificationController::class, 'store'])->name('store');
        Route::post('/emergency', [NotificationController::class, 'sendEmergency'])->name('send-emergency');
        Route::post('/test', [NotificationController::class, 'sendTest'])->name('send-test');
        Route::get('/{notification}', [NotificationController::class, 'show'])->name('show');
        Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/{notification}/archive', [NotificationController::class, 'markAsArchived'])->name('mark-archive');
        Route::post('/{notification}/action-taken', [NotificationController::class, 'markAsActionTaken'])->name('mark-action-taken');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-delete', [NotificationController::class, 'bulkDelete'])->name('bulk-delete');
        Route::get('/statistics', [NotificationController::class, 'statistics'])->name('statistics');
        Route::post('/admin/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])
            ->name('mark-as-read');
    });
    
    // Grafik Routes
    Route::get('/grafik', [GrafikController::class, 'index'])->name('admin.grafik.index');
    Route::get('/grafik/kategori', [GrafikController::class, 'byCategory'])->name('admin.grafik.category');
    Route::get('/dashboard', [GrafikController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/api/grafik-data', [GrafikController::class, 'apiData'])->name('admin.grafik.api');
});