<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class DonasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $donasi = Donasi::with('user')->latest()->get();
            
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'nullable|exists:users,id',
            'datalansia_id' => 'nullable|exists:datalansia,id',
            'jumlah' => 'required|integer|min:10000',
            'metode_pembayaran' => 'required|string',
            'nama_donatur' => 'required|string|max:100',
            'keterangan' => 'nullable|string',
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
            $data['bukti_pembayaran'] = '';
            
            $donasi = Donasi::create($data);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Donasi berhasil dibuat',
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
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $donasi = Donasi::with('user')->find($id);
            
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
    public function getByUser($userId)
    {
        try {
            $donasi = Donasi::where('user_id', $userId)
                ->orWhere('nama_donatur', 'like', "%$userId%") // fallback
                ->latest()
                ->get();
            
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
            
            $donasi->update([
                'status' => $request->status
            ]);
            
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
}