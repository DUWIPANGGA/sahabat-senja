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
    // Profile routes
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
        Route::get('/password/edit', [ProfileController::class, 'editPassword'])->name('edit-password');
        Route::put('/password/update', [ProfileController::class, 'updatePassword'])->name('update-password');
        Route::delete('/photo/delete', [ProfileController::class, 'deletePhoto'])->name('delete-photo');
        Route::post('/photo/upload', [ProfileController::class, 'uploadPhoto'])->name('profile.upload-photo');
                Route::post('/photo/upload', [ProfileController::class, 'uploadPhoto'])->name('upload-photo'); 

    });
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
    
    Route::prefix('donasi')->name('admin.donasi.')->group(function () {
        Route::get('/donasi/export-test', function() {
    return 'Donasi export test works!';
})->name('admin.donasi.export-test');
        Route::get('/', [DonasiController::class, 'index'])->name('index');
        Route::get('/{donasi}', [DonasiController::class, 'show'])->name('show');
        Route::post('/{donasi}/status', [DonasiController::class, 'updateStatus'])->name('updateStatus');
        Route::get('/export', [DonasiController::class, 'export'])->name('export');
        Route::post('/export-filtered', [DonasiController::class, 'exportFiltered'])->name('export-filtered');
        Route::get('/export-summary', [DonasiController::class, 'exportSummary'])->name('export-summary');
        Route::get('/check-pending', [DonasiController::class, 'checkPending'])->name('check-pending');
    });
    // Data Perawat (admin)
    Route::get('/DataPerawat', [DataPerawatController::class, 'index'])->name('admin.DataPerawat.index');
    Route::get('/DataPerawat/tambah', [DataPerawatController::class, 'create'])->name('admin.DataPerawat.create');
    Route::post('/DataPerawat/store', [DataPerawatController::class, 'store'])->name('admin.DataPerawat.store');
    Route::get('/DataPerawat/edit/{id}', [DataPerawatController::class, 'edit'])->name('admin.DataPerawat.edit');
    Route::post('/DataPerawat/update/{id}', [DataPerawatController::class, 'update'])->name('admin.DataPerawat.update');
    Route::get('/DataPerawat/hapus/{id}', [DataPerawatController::class, 'destroy'])->name('admin.DataPerawat.destroy');
    
    Route::prefix('kampanye')->name('admin.kampanye.')->group(function () {
            Route::get('/', [KampanyeDonasiController::class, 'index'])->name('index');
            Route::get('/create', [KampanyeDonasiController::class, 'create'])->name('create');
            Route::post('/', [KampanyeDonasiController::class, 'store'])->name('store');
            Route::get('/{kampanye}/edit', [KampanyeDonasiController::class, 'edit'])->name('edit');
            Route::put('/{kampanye}', [KampanyeDonasiController::class, 'update'])->name('update');
            Route::delete('/{kampanye}', [KampanyeDonasiController::class, 'destroy'])->name('destroy');
            Route::get('/{id}', [KampanyeDonasiController::class, 'show'])->name('show');
            Route::post('/{kampanye}/status', [KampanyeDonasiController::class, 'updateStatus'])->name('updateStatus');
            
            // Export data
            Route::get('/export/excel', [KampanyeDonasiController::class, 'exportExcel'])->name('export.excel');
            Route::get('/export/pdf', [KampanyeDonasiController::class, 'exportPdf'])->name('export.pdf');
        });
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

Route::post('/notifications/{notification}/mark-as-archived', [NotificationController::class, 'markAsArchived'])
    ->name('mark-as-archived');

Route::post('/notifications/{notification}/mark-as-action-taken', [NotificationController::class, 'markAsActionTaken'])
    ->name('mark-as-action-taken');
    });
    Route::get('/grafik', [GrafikController::class, 'index'])->name('admin.grafik.index');
    Route::get('/grafik/kategori', [GrafikController::class, 'byCategory'])->name('admin.grafik.category');
    Route::get('/dashboard', [GrafikController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/api/grafik-data', [GrafikController::class, 'apiData'])->name('admin.grafik.api');
//     Route::prefix('donasi')->group(function () {
//     Route::get('/', [DonasiController::class, 'index']);
//     Route::post('/', [DonasiController::class, 'store']);
//     Route::get('/{id}', [DonasiController::class, 'show']);
//     Route::get('/user/{userId}', [DonasiController::class, 'getByUser']);
//     Route::put('/{id}/bukti', [DonasiController::class, 'updateBukti']);
//     Route::put('/{id}/status', [DonasiController::class, 'updateStatus']);
// });
});