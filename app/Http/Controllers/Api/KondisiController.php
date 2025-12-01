<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kondisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KondisiController extends Controller
{
    public function index()
    {
        try {
            $kondisi = Kondisi::all();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data kondisi berhasil diambil',
                'data' => $kondisi
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data kondisi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_lansia' => 'required|string|max:100',
            'tanggal' => 'required|date',
            'tekanan_darah' => 'nullable|string|max:20',
            'nadi' => 'nullable|string|max:10',
            'nafsu_makan' => 'nullable|string|max:50',
            'status_obat' => 'nullable|string|max:50',
            'catatan' => 'nullable|string',
            'status' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $kondisi = Kondisi::create($request->all());
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data kondisi berhasil disimpan',
                'data' => $kondisi
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data kondisi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $kondisi = Kondisi::find($id);
            
            if (!$kondisi) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data kondisi tidak ditemukan'
                ], 404);
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data kondisi berhasil diambil',
                'data' => $kondisi
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data kondisi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getByNama($nama)
    {
        try {
            $kondisi = Kondisi::where('nama_lansia', $nama)->get();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data kondisi berhasil diambil',
                'data' => $kondisi
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data kondisi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getToday($datalansiaId, $tanggal)
{
    try {
        // Cari berdasarkan datalansia_id
        $kondisi = Kondisi::where('datalansia_id', $datalansiaId)
            ->whereDate('tanggal', $tanggal)
            ->first();
        
        if (!$kondisi) {
            return response()->json([
                'status' => 'success',
                'message' => 'Tidak ada data untuk hari ini',
                'data' => null
            ], 200);
        }
        
        // Load relasi
        $kondisi->load(['datalansia', 'perawat']);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Data kondisi hari ini berhasil diambil',
            'data' => $kondisi
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Gagal mengambil data kondisi hari ini',
            'error' => $e->getMessage()
        ], 500);
    }
}

}