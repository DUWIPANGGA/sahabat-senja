<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KeuanganController extends Controller
{
    // ============ PEMASUKAN ============
    
    /**
     * Get all pemasukan
     */
    public function getPemasukan()
    {
        try {
            $pemasukan = Pemasukan::all();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data pemasukan berhasil diambil',
                'data' => $pemasukan
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data pemasukan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create pemasukan
     */
    public function createPemasukan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal' => 'required|date',
            'sumber' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $pemasukan = Pemasukan::create($request->all());
            
            return response()->json([
                'status' => 'success',
                'message' => 'Pemasukan berhasil ditambahkan',
                'data' => $pemasukan
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menambahkan pemasukan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ============ PENGELUARAN ============
    
    /**
     * Get all pengeluaran
     */
    public function getPengeluaran()
    {
        try {
            $pengeluaran = Pengeluaran::all();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data pengeluaran berhasil diambil',
                'data' => $pengeluaran
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data pengeluaran',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create pengeluaran
     */
    public function createPengeluaran(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal' => 'required|date',
            'keterangan' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $pengeluaran = Pengeluaran::create($request->all());
            
            return response()->json([
                'status' => 'success',
                'message' => 'Pengeluaran berhasil ditambahkan',
                'data' => $pengeluaran
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menambahkan pengeluaran',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ============ LAPORAN KEUANGAN ============
    
    /**
     * Get laporan keuangan (pemasukan - pengeluaran)
     */
    public function getLaporanKeuangan()
    {
        try {
            $totalPemasukan = Pemasukan::sum('jumlah');
            $totalPengeluaran = Pengeluaran::sum('jumlah');
            $saldo = $totalPemasukan - $totalPengeluaran;
            
            $pemasukanTerbaru = Pemasukan::orderBy('tanggal', 'desc')->limit(10)->get();
            $pengeluaranTerbaru = Pengeluaran::orderBy('tanggal', 'desc')->limit(10)->get();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Laporan keuangan berhasil diambil',
                'data' => [
                    'total_pemasukan' => $totalPemasukan,
                    'total_pengeluaran' => $totalPengeluaran,
                    'saldo' => $saldo,
                    'pemasukan_terbaru' => $pemasukanTerbaru,
                    'pengeluaran_terbaru' => $pengeluaranTerbaru,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil laporan keuangan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}