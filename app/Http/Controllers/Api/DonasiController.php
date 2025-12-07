<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donasi;
use App\Models\KampanyeDonasi;
use App\Models\Datalansia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Midtrans\Config;
use Midtrans\Snap;

class DonasiController extends Controller
{
    public function __construct()
    {
        // Konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production', false);
        Config::$isSanitized = config('midtrans.is_sanitized', true);
        Config::$is3ds = config('midtrans.is_3ds', true);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $donasi = Donasi::with(['user', 'kampanye', 'datalansia'])->latest()->get();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data donasi berhasil diambil',
                'data' => $donasi
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data donasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage - Donasi dengan Midtrans
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kampanye_donasi_id' => 'nullable|exists:kampanye_donasi,id',
            'user_id' => 'nullable|exists:users,id',
            'datalansia_id' => 'nullable|exists:datalansia,id',
            'jumlah' => 'required|integer|min:1000',
            'metode_pembayaran' => 'required|string|in:midtrans,manual',
            'nama_donatur' => 'required|string|max:255',
            'email' => 'required|email',
            'telepon' => 'required|string|max:20',
            'keterangan' => 'nullable|string',
            'anonim' => 'boolean',
            'doa_harapan' => 'nullable|string',
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
            
            // Jika user tidak login, set user_id ke null
            if (!isset($data['user_id']) && Auth::check()) {
                $data['user_id'] = Auth::id();
            }
            
            // Set status berdasarkan metode pembayaran
            if ($data['metode_pembayaran'] === 'midtrans') {
                $data['status'] = 'pending';
            } else {
                $data['status'] = 'menunggu_verifikasi';
            }
            
            $data['bukti_pembayaran'] = '';
            
            // Buat donasi
            $donasi = Donasi::create($data);
            
            // Jika metode pembayaran Midtrans, buat Snap token
            if ($data['metode_pembayaran'] === 'midtrans') {
                $snapToken = $this->createMidtransTransaction($donasi);
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'Donasi berhasil dibuat',
                    'data' => [
                        'donasi' => $donasi,
                        'payment' => [
                            'snap_token' => $snapToken,
                            'client_key' => config('midtrans.client_key'),
                            'order_id' => $donasi->kode_donasi,
                            'amount' => $donasi->jumlah,
                        ]
                    ]
                ], 201);
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Donasi berhasil dibuat. Silakan upload bukti pembayaran.',
                'data' => $donasi
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membuat donasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create Midtrans transaction
     */
    private function createMidtransTransaction(Donasi $donasi)
    {
        try {
            // Generate kode donasi jika belum ada
            if (!$donasi->kode_donasi) {
                $donasi->kode_donasi = 'DON-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -8));
                $donasi->save();
            }

            // Transaction details
            $transactionDetails = [
                'order_id' => $donasi->kode_donasi,
                'gross_amount' => $donasi->jumlah,
            ];

            // Item details
            $itemDetails = [];
            
            // Jika ada kampanye
            if ($donasi->kampanye) {
                $itemDetails[] = [
                    'id' => 'kampanye-' . $donasi->kampanye->id,
                    'price' => $donasi->jumlah,
                    'quantity' => 1,
                    'name' => 'Donasi untuk ' . $donasi->kampanye->judul,
                    'category' => 'Donasi',
                    'merchant_name' => config('app.name', 'Lansia Care')
                ];
            } else {
                $itemDetails[] = [
                    'id' => 'donasi-' . $donasi->id,
                    'price' => $donasi->jumlah,
                    'quantity' => 1,
                    'name' => 'Donasi Kemanusiaan',
                    'category' => 'Donasi',
                    'merchant_name' => config('app.name', 'Lansia Care')
                ];
            }

            // Customer details
            $customerDetails = [
                'first_name' => $donasi->nama_donatur,
                'email' => $donasi->email,
                'phone' => $donasi->telepon,
            ];

            // Custom fields untuk tracking
            $customField = [
                'donasi_id' => $donasi->id,
                'user_id' => $donasi->user_id,
                'kampanye_id' => $donasi->kampanye_donasi_id,
            ];

            // Callback URLs
            $callbacks = [
                'finish' => route('payment.callback.finish'),
                'error' => route('payment.callback.error'),
                'pending' => route('payment.callback.pending'),
            ];

            $params = [
                'transaction_details' => $transactionDetails,
                'item_details' => $itemDetails,
                'customer_details' => $customerDetails,
                'custom_field1' => json_encode($customField),
                'callbacks' => $callbacks,
            ];

            // Get Snap token
            $snapToken = Snap::getSnapToken($params);
            
            return $snapToken;
            
        } catch (\Exception $e) {
            throw new \Exception('Gagal membuat transaksi Midtrans: ' . $e->getMessage());
        }
    }

    /**
     * Handle Midtrans notification (webhook)
     */
    public function handleMidtransNotification(Request $request)
    {
        try {
            $payload = $request->all();
            
            // Verifikasi signature jika diperlukan
            $orderId = $payload['order_id'];
            $statusCode = $payload['status_code'];
            $grossAmount = $payload['gross_amount'];
            $transactionStatus = $payload['transaction_status'];
            $fraudStatus = $payload['fraud_status'] ?? null;
            
            // Cari donasi berdasarkan kode donasi
            $donasi = Donasi::where('kode_donasi', $orderId)->first();
            
            if (!$donasi) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Donasi tidak ditemukan'
                ], 404);
            }
            
            // Verifikasi amount
            if ((int)$grossAmount != (int)$donasi->jumlah) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Jumlah pembayaran tidak sesuai'
                ], 400);
            }
            
            // Update status berdasarkan transaction status
            $newStatus = $this->mapTransactionStatus($transactionStatus, $fraudStatus);
            
            if ($newStatus) {
                $donasi->update([
                    'status' => $newStatus,
                    'metode_pembayaran' => 'midtrans'
                ]);
                
                // Jika status success, update dana terkumpul
                if ($newStatus === 'sukses' && $donasi->kampanye) {
                    $donasi->kampanye->increment('dana_terkumpul', $donasi->jumlah);
                    $donasi->kampanye->increment('jumlah_donatur');
                }
                
                // Log the notification
                \Log::info('Midtrans Notification', [
                    'order_id' => $orderId,
                    'status' => $transactionStatus,
                    'new_status' => $newStatus,
                    'donasi_id' => $donasi->id
                ]);
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Notification processed successfully'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Midtrans Notification Error', [
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
     * Map Midtrans transaction status to our status
     */
    private function mapTransactionStatus($transactionStatus, $fraudStatus = null)
    {
        switch ($transactionStatus) {
            case 'capture':
                if ($fraudStatus == 'accept') {
                    return 'sukses';
                } else if ($fraudStatus == 'challenge') {
                    return 'menunggu_verifikasi';
                } else {
                    return 'pending';
                }
                
            case 'settlement':
                return 'sukses';
                
            case 'pending':
                return 'pending';
                
            case 'deny':
            case 'cancel':
            case 'expire':
                return 'gagal';
                
            default:
                return null;
        }
    }

    /**
     * Get Snap token for existing donasi
     */
    public function getSnapToken($id)
    {
        try {
            $donasi = Donasi::find($id);
            
            if (!$donasi) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data donasi tidak ditemukan'
                ], 404);
            }
            
            // Hanya untuk donasi dengan metode midtrans
            if ($donasi->metode_pembayaran !== 'midtrans') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Metode pembayaran bukan Midtrans'
                ], 400);
            }
            
            // Hanya untuk status pending
            if ($donasi->status !== 'pending') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Status donasi sudah tidak pending'
                ], 400);
            }
            
            $snapToken = $this->createMidtransTransaction($donasi);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Snap token berhasil dibuat',
                'data' => [
                    'snap_token' => $snapToken,
                    'client_key' => config('midtrans.client_key'),
                    'order_id' => $donasi->kode_donasi,
                    'amount' => $donasi->jumlah,
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membuat snap token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check payment status
     */
    public function checkPaymentStatus($kodeDonasi)
    {
        try {
            $donasi = Donasi::where('kode_donasi', $kodeDonasi)->first();
            
            if (!$donasi) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data donasi tidak ditemukan'
                ], 404);
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Status pembayaran berhasil diambil',
                'data' => [
                    'kode_donasi' => $donasi->kode_donasi,
                    'status' => $donasi->status,
                    'jumlah' => $donasi->jumlah,
                    'jumlah_formatted' => 'Rp ' . number_format($donasi->jumlah, 0, ',', '.'),
                    'metode_pembayaran' => $donasi->metode_pembayaran,
                    'created_at' => $donasi->created_at->format('d-m-Y H:i:s'),
                    'nama_donatur' => $donasi->anonim ? 'Anonim' : $donasi->nama_donatur,
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil status pembayaran',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $donasi = Donasi::with(['user', 'kampanye', 'datalansia'])->find($id);
            
            if (!$donasi) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data donasi tidak ditemukan'
                ], 404);
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data donasi berhasil diambil',
                'data' => $donasi
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data donasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get donasi by user
     */
    public function getByUser($userId = null)
    {
        try {
            $userId = $userId ?? Auth::id();
            
            $donasi = Donasi::where('user_id', $userId)
                ->with(['kampanye'])
                ->latest()
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'kode_donasi' => $item->kode_donasi,
                        'jumlah' => $item->jumlah,
                        'jumlah_formatted' => 'Rp ' . number_format($item->jumlah, 0, ',', '.'),
                        'status' => $item->status,
                        'metode_pembayaran' => $item->metode_pembayaran,
                        'created_at' => $item->created_at->format('d-m-Y H:i'),
                        'kampanye' => $item->kampanye ? [
                            'judul' => $item->kampanye->judul,
                            'gambar' => $item->kampanye->gambar,
                        ] : null,
                        'anonim' => $item->anonim,
                        'nama_donatur' => $item->anonim ? 'Anonim' : $item->nama_donatur,
                    ];
                });
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data donasi berhasil diambil',
                'data' => $donasi
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data donasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update bukti pembayaran
     */
    public function updateBukti(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'bukti_pembayaran' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $donasi = Donasi::find($id);
            
            if (!$donasi) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data donasi tidak ditemukan'
                ], 404);
            }
            
            // Hanya bisa upload bukti untuk metode manual
            if ($donasi->metode_pembayaran === 'midtrans') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak bisa upload bukti untuk metode pembayaran Midtrans'
                ], 400);
            }
            
            $donasi->update([
                'bukti_pembayaran' => $request->bukti_pembayaran,
                'status' => 'menunggu_verifikasi'
            ]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Bukti pembayaran berhasil diupload',
                'data' => $donasi
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengupload bukti',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update status donasi
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,menunggu_verifikasi,sukses,gagal',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $donasi = Donasi::find($id);
            
            if (!$donasi) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data donasi tidak ditemukan'
                ], 404);
            }
            
            $oldStatus = $donasi->status;
            $newStatus = $request->status;
            
            $donasi->update([
                'status' => $newStatus
            ]);
            
            // Jika status berubah dari non-sukses ke sukses, update dana terkumpul
            if ($oldStatus !== 'sukses' && $newStatus === 'sukses' && $donasi->kampanye) {
                $donasi->kampanye->increment('dana_terkumpul', $donasi->jumlah);
                $donasi->kampanye->increment('jumlah_donatur');
            }
            
            // Jika status berubah dari sukses ke non-sukses, kurangi dana terkumpul
            if ($oldStatus === 'sukses' && $newStatus !== 'sukses' && $donasi->kampanye) {
                $donasi->kampanye->decrement('dana_terkumpul', $donasi->jumlah);
                $donasi->kampanye->decrement('jumlah_donatur');
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Status donasi berhasil diupdate',
                'data' => $donasi
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengupdate status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available payment methods
     */
    public function getPaymentMethods()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Metode pembayaran berhasil diambil',
            'data' => [
                [
                    'code' => 'midtrans',
                    'name' => 'Midtrans Payment Gateway',
                    'description' => 'Pembayaran via Midtrans (Transfer Bank, E-Wallet, QRIS)',
                    'min_amount' => 1000,
                ],
                [
                    'code' => 'manual',
                    'name' => 'Manual Transfer',
                    'description' => 'Transfer manual ke rekening bank kami',
                    'min_amount' => 10000,
                ]
            ]
        ]);
    }
}