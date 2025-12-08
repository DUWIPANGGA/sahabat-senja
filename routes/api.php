<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\IuranController;
use App\Http\Controllers\Api\DonasiController;
use App\Http\Controllers\Api\KondisiController;
use App\Http\Controllers\Api\KeuanganController;
use App\Http\Controllers\Api\AuthMobileController;
use App\Http\Controllers\Api\DatalansiaController;
use App\Http\Controllers\Api\JadwalObatController;
use App\Http\Controllers\Api\DataPerawatController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\Api\TrackingObatController;
use App\Http\Controllers\Api\KampanyeDonasiController;
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
    Route::prefix('DataPerawat')->group(function () {
        Route::get('/', [DataPerawatController::class, 'index']);
        Route::post('/', [DataPerawatController::class, 'store']);
        Route::get('/{id}', [DataPerawatController::class, 'show']);
        Route::put('/{id}', [DataPerawatController::class, 'update']);
        Route::delete('/{id}', [DataPerawatController::class, 'destroy']);
        
        // 🔹 Route tambahan
        Route::get('/active/count', [DataPerawatController::class, 'activeCount']);
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

    // Donasi routes dengan Midtrans
Route::prefix('donasi')->group(function () {
    Route::get('/', [DonasiController::class, 'index']);
    Route::post('/', [DonasiController::class, 'store']);
    Route::get('/{id}', [DonasiController::class, 'show']);
    Route::post('/{id}/bukti', [DonasiController::class, 'updateBukti']);
    Route::post('/{kodeDonasi}/update-status', [DonasiController::class, 'updateStatus']);
    
    // Midtrans specific
    Route::post('/notification', [DonasiController::class, 'handleMidtransNotification']);
    Route::get('/{id}/snap-token', [DonasiController::class, 'getSnapToken']);
    Route::get('/check/{kodeDonasi}', [DonasiController::class, 'checkPaymentStatus']);
    Route::get('/user/{userId?}', [DonasiController::class, 'getByUser']);
    Route::get('/payment-methods', [DonasiController::class, 'getPaymentMethods']);
    // routes/api.php
});

// Callback routes untuk Midtrans
Route::get('/payment/callback/finish', function () {
    return response()->json(['message' => 'Payment finished - handle in frontend']);
})->name('payment.callback.finish');

Route::get('/payment/callback/error', function () {
    return response()->json(['message' => 'Payment error - handle in frontend']);
})->name('payment.callback.error');

Route::get('/payment/callback/pending', function () {
    return response()->json(['message' => 'Payment pending - handle in frontend']);
})->name('payment.callback.pending');
// Kampanye Donasi Routes
Route::prefix('kampanye')->group(function () {
    // Public routes
    Route::get('/', [KampanyeDonasiController::class, 'index']);
    Route::get('/active', [KampanyeDonasiController::class, 'active']);
    Route::get('/featured', [KampanyeDonasiController::class, 'featured']);
    Route::get('/trending', [KampanyeDonasiController::class, 'trending']);
    Route::get('/categories', [KampanyeDonasiController::class, 'categories']);
    Route::get('/category/{category}', [KampanyeDonasiController::class, 'byCategory']);
    Route::get('/statistics', [KampanyeDonasiController::class, 'statistics']);
    Route::get('/elderly/{datalansiaId}', [KampanyeDonasiController::class, 'forElderly']);
    Route::get('/{slug}', [KampanyeDonasiController::class, 'show']);
    
    // Protected routes (admin only)
    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::post('/', [KampanyeDonasiController::class, 'store']);
        Route::put('/{id}', [KampanyeDonasiController::class, 'update']);
        Route::delete('/{id}', [KampanyeDonasiController::class, 'destroy']);
    });
});
Route::prefix('notifications')->group(function () {
        // Get notifications
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread-count', [NotificationController::class, 'getUnreadCount']);
        Route::get('/urgent', [NotificationController::class, 'getUrgentNotifications']);
        Route::get('/statistics', [NotificationController::class, 'getStatistics']);
        Route::get('/{id}', [NotificationController::class, 'show']);
        
        // Actions
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::post('/{id}/archive', [NotificationController::class, 'archive']);
        Route::post('/{id}/action-taken', [NotificationController::class, 'markAsActionTaken']);
        Route::delete('/{id}', [NotificationController::class, 'destroy']);
        Route::delete('/clear/read', [NotificationController::class, 'clearRead']);
        
        // Send notifications (admin/perawat only)
        Route::post('/send', [NotificationController::class, 'store']);
        Route::post('/send-batch', [NotificationController::class, 'sendBatch']);
    });
Route::prefix('iuran')->group(function () {
    // User routes (authenticated)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/', [IuranController::class, 'index']);
        Route::get('/statistics', [IuranController::class, 'statistics']);
        Route::get('/pending', [IuranController::class, 'pending']);
        Route::get('/upcoming', [IuranController::class, 'upcoming']);
        Route::get('/history', [IuranController::class, 'paymentHistory']);
        Route::get('/payment-methods', [IuranController::class, 'paymentMethods']);
        Route::get('/{id}', [IuranController::class, 'show']);
        Route::post('/{kodeIuran}/update-status', [IuranController::class, 'updateStatus']);
    Route::post('/{kodeIuran}/quick-update', [IuranController::class, 'quickUpdateStatus']);
    Route::post('/{kodeIuran}/upload-bukti', [IuranController::class, 'uploadBuktiPembayaran']);
        // Payment routes
        Route::post('/{id}/pay', [IuranController::class, 'updatePayment']);
        Route::post('/{id}/pay/midtrans', [IuranController::class, 'payWithMidtrans']);
    });

    // Admin only routes
    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::post('/', [IuranController::class, 'store']);
        Route::post('/{id}/verify', [IuranController::class, 'verifyPayment']);
    });

    // Midtrans notification (no auth required)
    Route::post('/notification', [IuranController::class, 'handleMidtransNotification']);
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