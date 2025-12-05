<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\KondisiController;
use App\Http\Controllers\Api\KeuanganController;
use App\Http\Controllers\Api\AuthMobileController;
use App\Http\Controllers\Api\DatalansiaController;
use App\Http\Controllers\Api\JadwalObatController;
use App\Http\Controllers\Api\DataperawatController;
use App\Http\Controllers\Api\TrackingObatController;
use App\Http\Controllers\Api\JadwalAktivitasController;

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthMobileController::class, 'register']);
    Route::post('/login', [AuthMobileController::class, 'login']);
    Route::post('/login/google', [AuthMobileController::class, 'googleLogin']);
    Route::post('/login/perawat', [AuthMobileController::class, 'perawatLogin']);
});

Route::get('/test', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'API Sahabat Senja berjalan dengan baik',
        'timestamp' => now()
    ]);
});

Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthMobileController::class, 'logout']);
        Route::get('/user', [AuthMobileController::class, 'user']);
        Route::put('/profile', [AuthMobileController::class, 'updateProfile']);
    });

    // Data Lansia routes
    Route::prefix('datalansia')->group(function () {
        Route::get('/', [DatalansiaController::class, 'index']);
        Route::post('/', [DatalansiaController::class, 'store']);
        Route::get('/{id}', [DatalansiaController::class, 'show']);
        Route::put('/{id}', [DatalansiaController::class, 'update']);
        Route::delete('/{id}', [DatalansiaController::class, 'destroy']);
        Route::get('/keluarga/{email}', [DatalansiaController::class, 'getByKeluarga']);
        
        // 🔹 Route tambahan untuk monitoring
        Route::get('/monitoring/aktif', [DatalansiaController::class, 'getAktifMonitoring']);
        Route::get('/search/{keyword}', [DatalansiaController::class, 'search']);
    });

    // Data Perawat routes
    Route::prefix('dataperawat')->group(function () {
        Route::get('/', [DataperawatController::class, 'index']);
        Route::post('/', [DataperawatController::class, 'store']);
        Route::get('/{id}', [DataperawatController::class, 'show']);
        Route::put('/{id}', [DataperawatController::class, 'update']);
        Route::delete('/{id}', [DataperawatController::class, 'destroy']);
        
        // 🔹 Route tambahan
        Route::get('/active/count', [DataperawatController::class, 'activeCount']);
    });

    // Keuangan routes
    Route::prefix('keuangan')->group(function () {
        Route::get('/pemasukan', [KeuanganController::class, 'getPemasukan']);
        Route::post('/pemasukan', [KeuanganController::class, 'createPemasukan']);
        Route::get('/pengeluaran', [KeuanganController::class, 'getPengeluaran']);
        Route::post('/pengeluaran', [KeuanganController::class, 'createPengeluaran']);
        Route::get('/laporan', [KeuanganController::class, 'getLaporanKeuangan']);
        
        // 🔹 Route tambahan
        Route::get('/summary/{periode}', [KeuanganController::class, 'getSummary']);
        Route::get('/latest/{limit}', [KeuanganController::class, 'getLatest']);
    });

    // 🔹 PERHATIAN: Update grouping ini untuk Kondisi
    Route::prefix('kondisi')->group(function () {
        Route::get('/', [KondisiController::class, 'index']);
        Route::post('/', [KondisiController::class, 'store']);
        Route::get('/{id}', [KondisiController::class, 'show']);
        
        // 🔹 Route yang dibutuhkan oleh Flutter app
        Route::get('/nama/{nama}', [KondisiController::class, 'getByNama']); // Ganti dari 'riwayat/{nama}'
        Route::get('/today/{nama}/{tanggal}', [KondisiController::class, 'getToday']);
        Route::get('/lansia/{nama}', [KondisiController::class, 'getByNamaLansia']); // Untuk riwayat detail
        
        // 🔹 Route tambahan untuk filtering
        Route::get('/filter/tanggal/{tanggal}', [KondisiController::class, 'getByTanggal']);
        Route::get('/filter/bulan/{tahun}/{bulan}', [KondisiController::class, 'getByBulan']);
        Route::get('/latest/all', [KondisiController::class, 'getLatestAll']);
        
        Route::put('/{id}', [KondisiController::class, 'update']);
        Route::delete('/{id}', [KondisiController::class, 'destroy']);
    });

    Route::prefix('jadwal')->group(function () {
        Route::get('/', [JadwalAktivitasController::class, 'index']);
        Route::post('/', [JadwalAktivitasController::class, 'store']);
        Route::put('/{id}/completed', [JadwalAktivitasController::class, 'updateCompleted']);
        Route::delete('/{id}', [JadwalAktivitasController::class, 'destroy']);
        Route::put('/{id}', [JadwalAktivitasController::class, 'update']);
        
        // 🔹 Route tambahan
        Route::get('/today', [JadwalAktivitasController::class, 'getToday']);
        Route::get('/lansia/{datalansiaId}', [JadwalAktivitasController::class, 'getByLansia']);
    });

    Route::prefix('jadwal-obat')->group(function () {
        Route::get('/', [JadwalObatController::class, 'index']);
        Route::post('/', [JadwalObatController::class, 'store']);
        Route::get('/{id}', [JadwalObatController::class, 'show']);
        Route::put('/{id}', [JadwalObatController::class, 'update']);
        Route::put('/{id}/selesai', [JadwalObatController::class, 'updateSelesai']);
        Route::put('/{id}/jam-minum', [JadwalObatController::class, 'updateJamMinum']);
        Route::delete('/{id}', [JadwalObatController::class, 'destroy']);
        
        // Custom endpoints
        Route::get('/lansia/{datalansiaId}', [JadwalObatController::class, 'byLansia']);
        Route::get('/aktif/hari-ini', [JadwalObatController::class, 'aktifHariIni']);
        
        // 🔹 Route tambahan
        Route::get('/today/pending', [JadwalObatController::class, 'getTodayPending']);
        Route::get('/summary', [JadwalObatController::class, 'getSummary']);
    });

    // Tracking Obat routes
    Route::prefix('tracking-obat')->group(function () {
        // CRUD endpoints
        Route::get('/', [TrackingObatController::class, 'index']);
        Route::post('/', [TrackingObatController::class, 'store']);
        Route::get('/{id}', [TrackingObatController::class, 'show']);
        Route::delete('/{id}', [TrackingObatController::class, 'destroy']);
        
        // Custom endpoints
        Route::get('/tanggal/{tanggal}', [TrackingObatController::class, 'byTanggal']);
        Route::get('/hari-ini', [TrackingObatController::class, 'hariIni']);
        Route::get('/lansia/{datalansiaId}', [TrackingObatController::class, 'byLansia']);
        Route::get('/statistics', [TrackingObatController::class, 'statistics']);
        
        // Update endpoints
        Route::put('/{id}/status', [TrackingObatController::class, 'updateStatus']);
        Route::put('/{id}/catatan', [TrackingObatController::class, 'updateCatatan']);
        
        // Generate tracking
        Route::post('/generate/hari-ini', [TrackingObatController::class, 'generateHariIni']);
        
        // 🔹 Route tambahan
        Route::get('/today/completion', [TrackingObatController::class, 'getTodayCompletion']);
    });

    Route::prefix('chat')->group(function () {
        Route::get('/conversations', [ChatController::class, 'conversations']);
        Route::get('/search-users', [ChatController::class, 'searchUsers']);
        
        // Messages
        Route::get('/messages/{userId}', [ChatController::class, 'messages']);
        Route::post('/send', [ChatController::class, 'sendMessage']);
        Route::post('/mark-read', [ChatController::class, 'markAsRead']);
        
        // Manage
        Route::delete('/message/{messageId}', [ChatController::class, 'deleteMessage']);
        Route::delete('/clear/{userId}', [ChatController::class, 'clearConversation']);
        
        // Stats
        Route::get('/unread-count', [ChatController::class, 'unreadCount']);
        Route::get('/statistics', [ChatController::class, 'statistics']);
        
        // 🔹 Route tambahan
        Route::get('/recent', [ChatController::class, 'getRecentChats']);
    });

    // ============ ROLE-BASED ROUTES ============
    
    // Admin only routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', function () {
            return response()->json(['message' => 'Admin Dashboard']);
        });
        
        // 🔹 Dashboard statistics
        Route::get('/admin/statistics', function() {
            // Endpoint untuk statistik dashboard admin
            return response()->json([
                'status' => 'success',
                'data' => [
                    'total_lansia' => \App\Models\Datalansia::count(),
                    'total_perawat' => \App\Models\User::where('role', 'perawat')->count(),
                    'total_keluarga' => \App\Models\User::where('role', 'keluarga')->count(),
                    'kondisi_hari_ini' => \App\Models\Kondisi::whereDate('created_at', today())->count(),
                ]
            ]);
        });
    });

    // Perawat only routes
    Route::middleware('role:perawat')->group(function () {
        Route::get('/perawat/dashboard', function () {
            return response()->json(['message' => 'Perawat Dashboard']);
        });
        
        // 🔹 Routes khusus perawat
        Route::prefix('perawat')->group(function () {
            Route::get('/assigned-lansia', function() {
                // Logika untuk mengambil lansia yang ditugaskan ke perawat
                $perawatId = auth()->id();
                $lansia = \App\Models\Kondisi::where('perawat_id', $perawatId)
                    ->with('datalansia')
                    ->get()
                    ->pluck('datalansia')
                    ->unique();
                
                return response()->json([
                    'status' => 'success',
                    'data' => $lansia
                ]);
            });
            
            Route::get('/today-tasks', function() {
                $perawatId = auth()->id();
                $today = now()->format('Y-m-d');
                
                $tasks = [
                    'kondisi_to_check' => \App\Models\Kondisi::whereDate('tanggal', $today)
                        ->where('perawat_id', $perawatId)
                        ->count(),
                    'obat_to_give' => \App\Models\TrackingObat::whereDate('tanggal', $today)
                        ->where('perawat_id', $perawatId)
                        ->where('status', 'belum')
                        ->count(),
                ];
                
                return response()->json([
                    'status' => 'success',
                    'data' => $tasks
                ]);
            });
        });
    });

    // Keluarga only routes
    Route::middleware('role:keluarga')->group(function () {
        Route::get('/keluarga/dashboard', function () {
            return response()->json(['message' => 'Keluarga Dashboard']);
        });
        
        // 🔹 Routes khusus keluarga
        Route::prefix('keluarga')->group(function () {
            Route::get('/my-lansia', [DatalansiaController::class, 'getByKeluargaEmail']);
            Route::get('/kondisi-terbaru', function() {
                $userEmail = auth()->user()->email;
                $lansia = \App\Models\Datalansia::where('email_anak', $userEmail)->get();
                
                $latestKondisi = [];
                foreach ($lansia as $l) {
                    $kondisi = \App\Models\Kondisi::where('nama_lansia', $l->nama_lansia)
                        ->latest()
                        ->first();
                    
                    if ($kondisi) {
                        $latestKondisi[] = [
                            'lansia' => $l,
                            'kondisi' => $kondisi
                        ];
                    }
                }
                
                return response()->json([
                    'status' => 'success',
                    'data' => $latestKondisi
                ]);
            });
        });
    });
});