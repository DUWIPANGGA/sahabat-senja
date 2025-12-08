<?php

namespace App\Http\Controllers\Api;

use Log;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Datalansia;
use App\Models\IuranBulanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class IuranController extends Controller
{
    /**
     * Display a listing of iuran for the authenticated user
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            
            $query = IuranBulanan::with(['user', 'datalansia'])
                ->where('user_id', $user->id);
            
            // Filter by status
            if ($request->has('status') && $request->status !== '') {
                $query->where('status', $request->status);
            }
            
            // Filter by periode
            if ($request->has('periode') && $request->periode !== '') {
                $query->where('periode', $request->periode);
            }

            // Filter by lansia
            if ($request->has('datalansia_id') && $request->datalansia_id !== '') {
                $query->where('datalansia_id', $request->datalansia_id);
            }
            
            // Search
            if ($request->has('search') && $request->search !== '') {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nama_iuran', 'like', "%{$search}%")
                      ->orWhere('kode_iuran', 'like', "%{$search}%")
                      ->orWhere('deskripsi', 'like', "%{$search}%");
                });
            }

            // Sort
            $sortBy = $request->get('sort_by', 'tanggal_jatuh_tempo');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 10);
            $iuran = $query->paginate($perPage);
            
            $data = $iuran->map(function ($item) {
                return $this->transformIuran($item);
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Data iuran berhasil diambil',
                'data' => $data,
                'meta' => [
                    'current_page' => $iuran->currentPage(),
                    'last_page' => $iuran->lastPage(),
                    'per_page' => $iuran->perPage(),
                    'total' => $iuran->total(),
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error in IuranController@index: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data iuran',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Terjadi kesalahan server'
            ], 500);
        }
    }

    /**
     * Get iuran statistics for user
     */
    public function statistics()
    {
        try {
            $user = Auth::user();
            
            $totalIuran = IuranBulanan::where('user_id', $user->id)->count();
            $totalLunas = IuranBulanan::where('user_id', $user->id)->where('status', 'lunas')->count();
            $totalPending = IuranBulanan::where('user_id', $user->id)->where('status', 'pending')->count();
            $totalTerlambat = IuranBulanan::where('user_id', $user->id)->where('status', 'pending')
                ->where('tanggal_jatuh_tempo', '<', Carbon::now())->count();
            
            // Total tagihan pending
            $totalTagihan = IuranBulanan::where('user_id', $user->id)
                ->where('status', 'pending')
                ->sum('jumlah');

            return response()->json([
                'status' => 'success',
                'message' => 'Statistik iuran berhasil diambil',
                'data' => [
                    'total_iuran' => $totalIuran,
                    'total_lunas' => $totalLunas,
                    'total_pending' => $totalPending,
                    'total_terlambat' => $totalTerlambat,
                    'total_tagihan' => 'Rp ' . number_format($totalTagihan, 0, ',', '.'),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil statistik',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get pending iuran (tagihan yang harus dibayar)
     */
    public function pending(Request $request)
{
    try {
        $user = Auth::user();
        
        $query = IuranBulanan::with(['datalansia'])
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->orderBy('tanggal_jatuh_tempo', 'asc');

        // Filter by lansia
        if ($request->has('datalansia_id')) {
            $query->where('datalansia_id', $request->datalansia_id);
        }

        $iuran = $query->get()->map(function ($iuran) {
            return $this->transformIuran($iuran);
        });

        // Use total_bayar_numeric instead of total_bayar for calculation
        $totalTagihan = $iuran->sum(function ($item) {
            return $item['total_bayar_numeric'] ?? 0;
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Tagihan iuran berhasil diambil',
            'data' => $iuran,
            'total_tagihan' => 'Rp ' . number_format($totalTagihan, 0, ',', '.'),
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Gagal mengambil tagihan',
            'error' => $e->getMessage()
        ], 500);
    }
}

    /**
     * Display the specified iuran
     */
    public function show($id)
    {
        try {
            $user = Auth::user();
            
            $iuran = IuranBulanan::with(['user', 'datalansia'])
                ->where('user_id', $user->id)
                ->find($id);

            if (!$iuran) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data iuran tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Data iuran berhasil diambil',
                'data' => $this->transformIuran($iuran, true)
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data iuran',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new iuran (for admin only)
     */
    public function store(Request $request)
    {
        // Hanya admin yang bisa membuat iuran manual
        if (!Auth::user()->isAdmin()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized - Hanya admin yang dapat membuat iuran'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'datalansia_id' => 'nullable|exists:datalansia,id',
            'nama_iuran' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'jumlah' => 'required|numeric|min:1000',
            'periode' => 'required|date_format:Y-m',
            'tanggal_jatuh_tempo' => 'required|date',
            'metode_pembayaran' => 'nullable|string',
            'is_otomatis' => 'boolean',
            'interval_bulan' => 'nullable|integer|min:1|required_if:is_otomatis,true',
            'berlaku_dari' => 'nullable|date|required_if:is_otomatis,true',
            'berlaku_sampai' => 'nullable|date|after:berlaku_dari',
            'catatan_admin' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->all();
            $data['status'] = 'pending';
            
            // Generate kode iuran otomatis
            $data['kode_iuran'] = 'IUR-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -8));

            $iuran = IuranBulanan::create($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Iuran berhasil dibuat',
                'data' => $this->transformIuran($iuran)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membuat iuran',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update payment (upload bukti pembayaran)
     */
    public function updatePayment(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'metode_pembayaran' => 'required|string|in:transfer_bank,ewallet,qris,cash',
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg,pdf|max:5120',
            'catatan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            
            $iuran = IuranBulanan::where('user_id', $user->id)
                ->where('status', 'pending')
                ->find($id);

            if (!$iuran) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Iuran tidak ditemukan atau sudah dibayar'
                ], 404);
            }

            // Upload bukti pembayaran
            $buktiPath = null;
            if ($request->hasFile('bukti_pembayaran')) {
                $buktiPath = $request->file('bukti_pembayaran')->store('bukti_iuran', 'public');
            }

            // Update status menjadi menunggu_verifikasi
            $iuran->update([
                'metode_pembayaran' => $request->metode_pembayaran,
                'bukti_pembayaran' => $buktiPath,
                'catatan_admin' => $request->catatan,
                'status' => 'menunggu_verifikasi',
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.',
                'data' => $this->transformIuran($iuran)
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengupload bukti pembayaran',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process payment with Midtrans
     */
    public function payWithMidtrans(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'payment_method' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            
            $iuran = IuranBulanan::with(['user', 'datalansia'])
                ->where('user_id', $user->id)
                ->where('status', 'pending')
                ->find($id);

            if (!$iuran) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Iuran tidak ditemukan atau sudah dibayar'
                ], 404);
            }

            // Konfigurasi Midtrans
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production');
            \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
            \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

            // Data transaksi Midtrans
            $transactionDetails = [
                'order_id' => $iuran->kode_iuran . '-' . time(),
                'gross_amount' => $iuran->total_bayar,
            ];

            $itemDetails = [
                [
                    'id' => $iuran->kode_iuran,
                    'price' => $iuran->total_bayar,
                    'quantity' => 1,
                    'name' => $iuran->nama_iuran,
                    'category' => 'Iuran Bulanan',
                    'merchant_name' => config('app.name', 'Sahabat Senja'),
                ]
            ];

            $customerDetails = [
                'first_name' => $user->name,
                'email' => $user->email,
                'phone' => $user->no_telepon,
            ];

            // Tambahan data untuk callback
            $customField = [
                'iuran_id' => $iuran->id,
                'user_id' => $user->id,
                'type' => 'iuran',
            ];

            $params = [
                'transaction_details' => $transactionDetails,
                'item_details' => $itemDetails,
                'customer_details' => $customerDetails,
                'custom_field1' => json_encode($customField),
            ];

            // Tambahkan payment method jika dipilih
            if ($request->payment_method) {
                $params['enabled_payments'] = [$request->payment_method];
            }

            // Get Snap token
            $snapToken = \Midtrans\Snap::getSnapToken($params);

            // Update metode pembayaran
            $iuran->update([
                'metode_pembayaran' => 'midtrans',
                'metadata' => [
                    'midtrans_order_id' => $transactionDetails['order_id'],
                    'midtrans_snap_token' => $snapToken,
                ]
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Payment gateway berhasil dibuat',
                'data' => [
                    'iuran' => $this->transformIuran($iuran),
                    'payment' => [
                        'snap_token' => $snapToken,
                        'client_key' => config('midtrans.client_key'),
                        'order_id' => $transactionDetails['order_id'],
                        'amount' => $iuran->total_bayar,
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membuat transaksi Midtrans',
                'error' => $e->getMessage()
            ], 500);
        }
    }
/**
 * Update payment status from mobile app callback
 * Route: POST /api/iuran/{kodeIuran}/update-status
 */
public function updateStatus(Request $request, $kodeIuran)
{
    try {
        // Validasi request
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,success,lunas,failed,gagal,expired,canceled,menunggu_verifikasi',
            'transaction_id' => 'nullable|string',
            'payment_type' => 'nullable|string',
            'gross_amount' => 'nullable|numeric',
            'transaction_time' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Cari iuran berdasarkan kode_iuran
        $iuran = IuranBulanan::where('kode_iuran', $kodeIuran)->first();

        if (!$iuran) {
            return response()->json([
                'status' => 'error',
                'message' => 'Iuran tidak ditemukan',
                'code' => 'IURAN_NOT_FOUND',
                'kode_iuran' => $kodeIuran
            ], 404);
        }

        Log::info('Mobile Iuran Status Update by Kode', [
            'iuran_id' => $iuran->id,
            'kode_iuran' => $iuran->kode_iuran,
            'old_status' => $iuran->status,
            'new_status' => $request->status,
            'transaction_id' => $request->transaction_id,
            'user_id' => $iuran->user_id,
            'request_data' => $request->all()
        ]);

        // Map status dari mobile ke status sistem
        $status = $this->mapMobileStatus($request->status);
        
        // Simpan status lama untuk pengecekan
        $oldStatus = $iuran->status;
        
        DB::beginTransaction();

        // Update iuran
        $updateData = [
            'status' => $status,
            'metadata' => array_merge($iuran->metadata ?? [], [
                'mobile_callback' => [
                    'transaction_id' => $request->transaction_id,
                    'payment_type' => $request->payment_type,
                    'gross_amount' => $request->gross_amount,
                    'transaction_time' => $request->transaction_time,
                    'callback_time' => now()->toDateTimeString(),
                ]
            ])
        ];
        
        // Jika status berubah menjadi lunas, update tanggal bayar dan metode
        if ($status === 'lunas' && $oldStatus !== 'lunas') {
            $updateData['tanggal_bayar'] = $request->transaction_time ?? now();
            $updateData['metode_pembayaran'] = 'midtrans';
            
            // Generate iuran berikutnya jika recurring
            if ($iuran->is_otomatis) {
                $nextIuran = $iuran->generateNextIuran();
                if ($nextIuran) {
                    Log::info('Iuran recurring generated', [
                        'parent_iuran_id' => $iuran->id,
                        'next_iuran_id' => $nextIuran->id,
                        'next_kode_iuran' => $nextIuran->kode_iuran,
                        'next_tanggal_jatuh_tempo' => $nextIuran->tanggal_jatuh_tempo
                    ]);
                }
            }
        }
        
        $iuran->update($updateData);

        DB::commit();

        return response()->json([
            'status' => 'success',
            'message' => 'Status iuran berhasil diperbarui',
            'data' => [
                'id' => $iuran->id,
                'kode_iuran' => $iuran->kode_iuran,
                'status' => $iuran->status,
                'status_formatted' => $this->getStatusText($iuran->status),
                'jumlah' => (float) $iuran->jumlah,
                'jumlah_formatted' => 'Rp ' . number_format($iuran->jumlah, 0, ',', '.'),
                'total_bayar' => (float) $iuran->total_bayar,
                'total_bayar_formatted' => 'Rp ' . number_format($iuran->total_bayar, 0, ',', '.'),
                'denda' => (float) $iuran->denda,
                'denda_formatted' => 'Rp ' . number_format($iuran->denda, 0, ',', '.'),
                'transaction_id' => $request->transaction_id,
                'tanggal_bayar' => $iuran->tanggal_bayar ? $iuran->tanggal_bayar->format('Y-m-d H:i:s') : null,
                'updated_at' => $iuran->updated_at->format('Y-m-d H:i:s')
            ]
        ], 200);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Mobile Iuran Status Update Error', [
            'error' => $e->getMessage(),
            'request' => $request->all(),
            'kode_iuran' => $kodeIuran
        ]);

        return response()->json([
            'status' => 'error',
            'message' => 'Gagal memperbarui status: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Map mobile status to system status
 */
private function mapMobileStatus($mobileStatus)
{
    $statusMap = [
        'pending' => 'menunggu_verifikasi',
        'success' => 'lunas',
        'lunas' => 'lunas',
        'failed' => 'ditolak',
        'gagal' => 'ditolak',
        'expired' => 'ditolak',
        'canceled' => 'ditolak',
        'menunggu_verifikasi' => 'menunggu_verifikasi',
    ];
    
    return $statusMap[$mobileStatus] ?? $mobileStatus;
}

/**
 * Quick update status tanpa validasi (untuk Midtrans callback)
 * Route: POST /api/iuran/{kodeIuran}/quick-update
 */
public function quickUpdateStatus(Request $request, $kodeIuran)
{
    try {
        // Validasi minimal
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:lunas,ditolak,menunggu_verifikasi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $iuran = IuranBulanan::where('kode_iuran', $kodeIuran)->first();

        if (!$iuran) {
            return response()->json([
                'status' => 'error',
                'message' => 'Iuran tidak ditemukan'
            ], 404);
        }

        Log::info('Quick Iuran Status Update', [
            'kode_iuran' => $kodeIuran,
            'old_status' => $iuran->status,
            'new_status' => $request->status
        ]);

        DB::beginTransaction();

        $iuran->update([
            'status' => $request->status,
            'tanggal_bayar' => $request->status === 'lunas' ? now() : $iuran->tanggal_bayar,
        ]);

        DB::commit();

        return response()->json([
            'status' => 'success',
            'message' => 'Status iuran berhasil diperbarui',
            'kode_iuran' => $iuran->kode_iuran,
            'status' => $iuran->status,
        ], 200);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Quick Iuran Status Update Error', [
            'error' => $e->getMessage(),
            'kode_iuran' => $kodeIuran
        ]);

        return response()->json([
            'status' => 'error',
            'message' => 'Gagal memperbarui status'
        ], 500);
    }
}
    /**
     * Handle Midtrans notification for iuran
     */
    public function handleMidtransNotification(Request $request)
    {
        try {
            $payload = $request->all();
            
            $orderId = $payload['order_id'];
            $statusCode = $payload['status_code'];
            $grossAmount = $payload['gross_amount'];
            $transactionStatus = $payload['transaction_status'];
            $fraudStatus = $payload['fraud_status'] ?? null;
            
            // Extract iuran code from order_id (format: IUR-20231201-ABC123-1701234567)
            $parts = explode('-', $orderId);
            $kodeIuran = $parts[0] . '-' . $parts[1] . '-' . $parts[2];
            
            $iuran = IuranBulanan::where('kode_iuran', $kodeIuran)->first();
            
            if (!$iuran) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Iuran tidak ditemukan'
                ], 404);
            }
            
            // Map Midtrans status to iuran status
            $status = $this->mapPaymentStatus($transactionStatus, $fraudStatus);
            
            if ($status) {
                $updateData = [
                    'status' => $status,
                    'metadata' => array_merge($iuran->metadata ?? [], [
                        'midtrans_response' => $payload,
                        'verified_at' => now(),
                    ])
                ];
                
                // Jika status lunas, set tanggal bayar
                if ($status === 'lunas') {
                    $updateData['tanggal_bayar'] = now();
                }
                
                $iuran->update($updateData);
                
                // Log transaction
                \Log::info('Midtrans Iuran Notification', [
                    'kode_iuran' => $kodeIuran,
                    'order_id' => $orderId,
                    'status' => $transactionStatus,
                    'iuran_status' => $status,
                ]);
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Notification processed'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Midtrans Iuran Error', [
                'error' => $e->getMessage(),
                'payload' => $request->all()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to process notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify payment manually (admin only)
     */
    public function verifyPayment(Request $request, $id)
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized - Hanya admin yang dapat verifikasi'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:lunas,ditolak',
            'catatan_admin' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $iuran = IuranBulanan::find($id);
            
            if (!$iuran) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Iuran tidak ditemukan'
                ], 404);
            }

            if ($iuran->status !== 'menunggu_verifikasi') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Iuran tidak dalam status menunggu verifikasi'
                ], 400);
            }

            $updateData = [
                'status' => $request->status,
                'catatan_admin' => $request->catatan_admin,
            ];

            if ($request->status === 'lunas') {
                $updateData['tanggal_bayar'] = now();
            }

            $iuran->update($updateData);

            return response()->json([
                'status' => 'success',
                'message' => 'Verifikasi pembayaran berhasil',
                'data' => $this->transformIuran($iuran)
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memverifikasi pembayaran',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment history
     */
    public function paymentHistory(Request $request)
    {
        try {
            $user = Auth::user();
            
            $query = IuranBulanan::with(['datalansia'])
                ->where('user_id', $user->id)
                ->whereIn('status', ['lunas', 'ditolak'])
                ->orderBy('tanggal_bayar', 'desc');

            // Filter by periode
            if ($request->has('periode')) {
                $query->where('periode', $request->periode);
            }

            // Filter by date range
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('tanggal_bayar', [
                    $request->start_date,
                    $request->end_date
                ]);
            }

            $perPage = $request->get('per_page', 10);
            $history = $query->paginate($perPage);

            $data = $history->map(function ($iuran) {
                return $this->transformIuran($iuran);
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Riwayat pembayaran berhasil diambil',
                'data' => $data,
                'meta' => [
                    'current_page' => $history->currentPage(),
                    'last_page' => $history->lastPage(),
                    'per_page' => $history->perPage(),
                    'total' => $history->total(),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil riwayat pembayaran',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get upcoming iuran
     */
    public function upcoming()
    {
        try {
            $user = Auth::user();
            
            $upcoming = IuranBulanan::with(['datalansia'])
                ->where('user_id', $user->id)
                ->where('status', 'pending')
                ->where('tanggal_jatuh_tempo', '>=', Carbon::now())
                ->orderBy('tanggal_jatuh_tempo', 'asc')
                ->limit(5)
                ->get()
                ->map(function ($iuran) {
                    return $this->transformIuran($iuran);
                });

            return response()->json([
                'status' => 'success',
                'message' => 'Iuran mendatang berhasil diambil',
                'data' => $upcoming
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil iuran mendatang',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment methods
     */
    public function paymentMethods()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Metode pembayaran berhasil diambil',
            'data' => [
                [
                    'code' => 'transfer_bank',
                    'name' => 'Transfer Bank',
                    'description' => 'Transfer ke rekening bank kami',
                    'instructions' => 'Silakan transfer ke BCA 1234567890 a.n Sahabat Senja',
                ],
                [
                    'code' => 'ewallet',
                    'name' => 'E-Wallet',
                    'description' => 'Bayar via OVO, Gopay, Dana',
                    'instructions' => 'Scan QR code atau kirim ke nomor 081234567890',
                ],
                [
                    'code' => 'qris',
                    'name' => 'QRIS',
                    'description' => 'Bayar dengan scan QR code',
                    'instructions' => 'Scan QR code melalui aplikasi bank/e-wallet',
                ],
                [
                    'code' => 'cash',
                    'name' => 'Tunai',
                    'description' => 'Bayar tunai di kantor',
                    'instructions' => 'Datang langsung ke kantor Sahabat Senja',
                ],
                [
                    'code' => 'midtrans',
                    'name' => 'Midtrans',
                    'description' => 'Pembayaran online via Midtrans',
                    'instructions' => 'Pilih metode pembayaran yang tersedia di Midtrans',
                ],
            ]
        ], 200);
    }

    /**
     * Transform iuran data for API response
     */
    /**
 * Transform iuran data for API response
 */
private function transformIuran($iuran, $detail = false)
{
    // Parse tanggal jatuh tempo dengan format yang benar
    $tanggalJatuhTempo = null;
    if ($iuran->tanggal_jatuh_tempo) {
        try {
            $tanggalJatuhTempo = Carbon::parse($iuran->tanggal_jatuh_tempo);
        } catch (\Exception $e) {
            $tanggalJatuhTempo = null;
        }
    }
    
    // Parse periode
    $periodeFormatted = null;
    if ($iuran->periode) {
        try {
            $periodeFormatted = Carbon::createFromFormat('Y-m', $iuran->periode)->format('F Y');
        } catch (\Exception $e) {
            $periodeFormatted = $iuran->periode;
        }
    }
    
    // Parse tanggal bayar
    $tanggalBayar = null;
    if ($iuran->tanggal_bayar) {
        try {
            $tanggalBayar = Carbon::parse($iuran->tanggal_bayar);
        } catch (\Exception $e) {
            $tanggalBayar = null;
        }
    }
    
    // Parse tanggal berlaku
    $berlakuDari = null;
    if ($iuran->berlaku_dari) {
        try {
            $berlakuDari = Carbon::parse($iuran->berlaku_dari);
        } catch (\Exception $e) {
            $berlakuDari = null;
        }
    }
    
    $berlakuSampai = null;
    if ($iuran->berlaku_sampai) {
        try {
            $berlakuSampai = Carbon::parse($iuran->berlaku_sampai);
        } catch (\Exception $e) {
            $berlakuSampai = null;
        }
    }
    
    // Konversi field numerik ke float untuk menghindari string operation
    $jumlah = (float) $iuran->jumlah;
    $denda = (float) $iuran->denda;
    $totalBayar = (float) $iuran->total_bayar;
    
    $data = [
        'id' => $iuran->id,
        'kode_iuran' => $iuran->kode_iuran,
        'nama_iuran' => $iuran->nama_iuran,
        'deskripsi' => $iuran->deskripsi,
        'jumlah' => 'Rp ' . number_format($jumlah, 0, ',', '.'),
        'jumlah_numeric' => $jumlah,
        'periode' => $iuran->periode,
        'periode_formatted' => $periodeFormatted,
        'tanggal_jatuh_tempo' => $tanggalJatuhTempo ? $tanggalJatuhTempo->format('Y-m-d') : null,
        'tanggal_jatuh_tempo_formatted' => $tanggalJatuhTempo ? $tanggalJatuhTempo->format('d-m-Y') : null,
        'tanggal_bayar' => $tanggalBayar ? $tanggalBayar->format('Y-m-d H:i:s') : null,
        'tanggal_bayar_formatted' => $tanggalBayar ? $tanggalBayar->format('d-m-Y H:i') : null,
        'status' => $iuran->status,
        'status_formatted' => $this->getStatusText($iuran->status),
        'metode_pembayaran' => $iuran->metode_pembayaran,
        'bukti_pembayaran' => $iuran->bukti_pembayaran ? url('storage/' . $iuran->bukti_pembayaran) : null,
        'is_terlambat' => $iuran->is_terlambat ?? false,
        'denda' => 'Rp ' . number_format($denda, 0, ',', '.'),
        'denda_numeric' => $denda,
        'total_bayar' => 'Rp ' . number_format($totalBayar, 0, ',', '.'),
        'total_bayar_numeric' => $totalBayar,
        'is_otomatis' => (bool) $iuran->is_otomatis,
        'interval_bulan' => (int) $iuran->interval_bulan,
        'berlaku_dari' => $berlakuDari ? $berlakuDari->format('Y-m-d') : null,
        'berlaku_dari_formatted' => $berlakuDari ? $berlakuDari->format('d-m-Y') : null,
        'berlaku_sampai' => $berlakuSampai ? $berlakuSampai->format('Y-m-d') : null,
        'berlaku_sampai_formatted' => $berlakuSampai ? $berlakuSampai->format('d-m-Y') : null,
        'catatan_admin' => $iuran->catatan_admin,
        'created_at' => $iuran->created_at ? $iuran->created_at->format('Y-m-d H:i:s') : null,
        'created_at_formatted' => $iuran->created_at ? $iuran->created_at->format('d-m-Y H:i') : null,
        'datalansia' => $iuran->datalansia ? [
            'id' => $iuran->datalansia->id,
            'nama' => $iuran->datalansia->nama_lansia,
            'foto' => $iuran->datalansia->foto ? url('storage/' . $iuran->datalansia->foto) : null,
        ] : null,
    ];

    if ($detail) {
        $data['user'] = $iuran->user ? [
            'id' => $iuran->user->id,
            'name' => $iuran->user->name,
            'email' => $iuran->user->email,
            'no_telepon' => $iuran->user->no_telepon,
        ] : null;
        
        $data['metadata'] = $iuran->metadata;
    }

    return $data;
}

    /**
     * Map Midtrans status to iuran status
     */
    private function mapPaymentStatus($transactionStatus, $fraudStatus)
    {
        switch ($transactionStatus) {
            case 'capture':
                return $fraudStatus == 'accept' ? 'lunas' : 'menunggu_verifikasi';
                
            case 'settlement':
                return 'lunas';
                
            case 'pending':
                return 'menunggu_verifikasi';
                
            case 'deny':
            case 'cancel':
            case 'expire':
                return 'ditolak';
                
            default:
                return null;
        }
    }

    /**
     * Get status text in Indonesian
     */
    private function getStatusText($status)
    {
        $statuses = [
            'pending' => 'Menunggu Pembayaran',
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'lunas' => 'Lunas',
            'ditolak' => 'Ditolak',
            'terlambat' => 'Terlambat',
        ];
        
        return $statuses[$status] ?? $status;
    }
}