<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donasi;
use App\Models\KampanyeDonasi;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Create new donation and get Snap token
     */
    public function createDonation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kampanye_id' => 'required|exists:kampanye_donasi,id',
            'jumlah' => 'required|integer|min:1000',
            'nama_donatur' => 'required|string|max:255',
            'email' => 'required|email',
            'telepon' => 'required|string|max:20',
            'keterangan' => 'nullable|string',
            'anonim' => 'boolean',
            'doa_harapan' => 'nullable|string',
            'datalansia_id' => 'nullable|exists:datalansia,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Create donation record
            $donasi = Donasi::create([
                'kampanye_donasi_id' => $request->kampanye_id,
                'user_id' => Auth::id(),
                'datalansia_id' => $request->datalansia_id,
                'jumlah' => $request->jumlah,
                'metode_pembayaran' => 'midtrans',
                'status' => 'pending',
                'nama_donatur' => $request->nama_donatur,
                'email' => $request->email,
                'telepon' => $request->telepon,
                'keterangan' => $request->keterangan,
                'anonim' => $request->anonim ?? false,
                'doa_harapan' => $request->doa_harapan,
            ]);

            // Prepare customer details for Midtrans
            $customerDetails = [
                'first_name' => $request->nama_donatur,
                'email' => $request->email,
                'phone' => $request->telepon,
                'billing_address' => [
                    'first_name' => $request->nama_donatur,
                    'email' => $request->email,
                    'phone' => $request->telepon,
                ],
                'shipping_address' => [
                    'first_name' => $request->nama_donatur,
                    'email' => $request->email,
                    'phone' => $request->telepon,
                ]
            ];

            // Get Snap token
            $snapToken = $this->midtransService->createTransaction($donasi, $customerDetails);

            return response()->json([
                'success' => true,
                'message' => 'Donation created successfully',
                'data' => [
                    'snap_token' => $snapToken,
                    'order_id' => $donasi->kode_donasi,
                    'donation_id' => $donasi->id,
                    'amount' => $donasi->jumlah,
                    'client_key' => config('midtrans.client_key'),
                    'snap_js_url' => $this->midtransService->getSnapJsUrl(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create donation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle Midtrans notification (webhook)
     */
    public function handleNotification(Request $request)
    {
        $payload = $request->all();

        // Verify the notification
        $orderId = $payload['order_id'];
        $statusCode = $payload['status_code'];
        $grossAmount = $payload['gross_amount'];
        $transactionStatus = $payload['transaction_status'];
        $fraudStatus = $payload['fraud_status'] ?? null;

        // Find donation
        $donasi = Donasi::where('kode_donasi', $orderId)->first();

        if (!$donasi) {
            return response()->json(['message' => 'Donation not found'], 404);
        }

        // Update donation status
        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'accept') {
                $donasi->status = 'success';
                $donasi->save();
            }
        } elseif ($transactionStatus == 'settlement') {
            $donasi->status = 'success';
            $donasi->save();
        } elseif ($transactionStatus == 'pending') {
            $donasi->status = 'pending';
            $donasi->save();
        } elseif ($transactionStatus == 'deny' || $transactionStatus == 'expire' || $transactionStatus == 'cancel') {
            $donasi->status = 'failed';
            $donasi->save();
        }

        return response()->json(['message' => 'Notification processed']);
    }

    /**
     * Check donation status
     */
    public function checkStatus($kodeDonasi)
    {
        $donasi = Donasi::where('kode_donasi', $kodeDonasi)->first();

        if (!$donasi) {
            return response()->json([
                'success' => false,
                'message' => 'Donation not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'kode_donasi' => $donasi->kode_donasi,
                'status' => $donasi->status,
                'jumlah' => $donasi->jumlah,
                'jumlah_formatted' => $donasi->jumlah_formatted,
                'created_at' => $donasi->created_at,
                'kampanye' => $donasi->kampanye ? [
                    'id' => $donasi->kampanye->id,
                    'judul' => $donasi->kampanye->judul,
                ] : null
            ]
        ]);
    }

    /**
     * Get active campaigns for donation
     */
    public function getCampaigns()
    {
        $campaigns = KampanyeDonasi::where('status', 'active')
            ->whereDate('deadline', '>=', now())
            ->select('id', 'judul', 'target_dana', 'dana_terkumpul', 'gambar', 'deskripsi_singkat')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $campaigns
        ]);
    }

    /**
     * Get donation history for authenticated user
     */
    public function getDonationHistory()
    {
        $donations = Donasi::where('user_id', Auth::id())
            ->with('kampanye:id,judul')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($donasi) {
                return [
                    'kode_donasi' => $donasi->kode_donasi,
                    'jumlah' => $donasi->jumlah,
                    'jumlah_formatted' => $donasi->jumlah_formatted,
                    'status' => $donasi->status,
                    'created_at' => $donasi->created_at->format('d M Y H:i'),
                    'kampanye' => $donasi->kampanye ? [
                        'judul' => $donasi->kampanye->judul,
                    ] : null,
                    'anonim' => $donasi->anonim,
                    'nama_donatur' => $donasi->anonim ? 'Anonim' : $donasi->nama_donatur,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $donations
        ]);
    }
}