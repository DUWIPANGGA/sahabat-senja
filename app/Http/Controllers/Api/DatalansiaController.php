<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Datalansia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DatalansiaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $datalansia = Datalansia::all();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data lansia berhasil diambil',
                'data' => $datalansia
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data lansia',
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
            'nama_lansia' => 'required|string|max:100',
            'umur_lansia' => 'required|integer|min:40|max:160',
            'tempat_lahir_lansia' => 'nullable|string|max:100',
            'tanggal_lahir_lansia' => 'nullable|date',
            'jenis_kelamin_lansia' => 'nullable|in:Laki-laki,Perempuan',
            'gol_darah_lansia' => 'nullable|string|max:5',
            'riwayat_penyakit_lansia' => 'nullable|string|max:255',
            'alergi_lansia' => 'nullable|string|max:255',
            'obat_rutin_lansia' => 'nullable|string|max:255',
            'nama_anak' => 'required|string|max:100',
            'alamat_lengkap' => 'required|string|max:255',
            'no_hp_anak' => 'required|string|max:15',
            'email_anak' => 'required|string|email|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $datalansia = Datalansia::create($request->all());
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data lansia berhasil ditambahkan',
                'data' => $datalansia
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menambahkan data lansia',
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
            $datalansia = Datalansia::find($id);
            
            if (!$datalansia) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data lansia tidak ditemukan'
                ], 404);
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data lansia berhasil diambil',
                'data' => $datalansia
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data lansia',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_lansia' => 'sometimes|string|max:100',
            'umur_lansia' => 'sometimes|integer|min:40|max:160',
            'tempat_lahir_lansia' => 'nullable|string|max:100',
            'tanggal_lahir_lansia' => 'nullable|date',
            'jenis_kelamin_lansia' => 'nullable|in:Laki-laki,Perempuan',
            'gol_darah_lansia' => 'nullable|string|max:5',
            'riwayat_penyakit_lansia' => 'nullable|string|max:255',
            'alergi_lansia' => 'nullable|string|max:255',
            'obat_rutin_lansia' => 'nullable|string|max:255',
            'nama_anak' => 'sometimes|string|max:100',
            'alamat_lengkap' => 'sometimes|string|max:255',
            'no_hp_anak' => 'sometimes|string|max:15',
            'email_anak' => 'sometimes|string|email|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $datalansia = Datalansia::find($id);
            
            if (!$datalansia) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data lansia tidak ditemukan'
                ], 404);
            }
            
            $datalansia->update($request->all());
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data lansia berhasil diperbarui',
                'data' => $datalansia
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui data lansia',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $datalansia = Datalansia::find($id);
            
            if (!$datalansia) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data lansia tidak ditemukan'
                ], 404);
            }
            
            $datalansia->delete();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data lansia berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus data lansia',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get data lansia by keluarga (email anak)
     */
    public function getByKeluarga($email)
    {
        try {
            $datalansia = Datalansia::where('email_anak', $email)->get();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data lansia berhasil diambil',
                'data' => $datalansia
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data lansia',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}