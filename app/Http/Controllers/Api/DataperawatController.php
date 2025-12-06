<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DataPerawat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DataPerawatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $DataPerawat = DataPerawat::all();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data perawat berhasil diambil',
                'data' => $DataPerawat
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data perawat',
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
            'nama' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:DataPerawat',
            'alamat' => 'required|string',
            'no_hp' => 'required|string|max:20',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $DataPerawat = DataPerawat::create($request->all());
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data perawat berhasil ditambahkan',
                'data' => $DataPerawat
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menambahkan data perawat',
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
            $DataPerawat = DataPerawat::find($id);
            
            if (!$DataPerawat) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data perawat tidak ditemukan'
                ], 404);
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data perawat berhasil diambil',
                'data' => $DataPerawat
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data perawat',
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
            'nama' => 'sometimes|string|max:100',
            'email' => 'sometimes|string|email|max:100|unique:DataPerawat,email,' . $id,
            'alamat' => 'sometimes|string',
            'no_hp' => 'sometimes|string|max:20',
            'jenis_kelamin' => 'sometimes|in:Laki-laki,Perempuan',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $DataPerawat = DataPerawat::find($id);
            
            if (!$DataPerawat) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data perawat tidak ditemukan'
                ], 404);
            }
            
            $DataPerawat->update($request->all());
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data perawat berhasil diperbarui',
                'data' => $DataPerawat
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui data perawat',
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
            $DataPerawat = DataPerawat::find($id);
            
            if (!$DataPerawat) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data perawat tidak ditemukan'
                ], 404);
            }
            
            $DataPerawat->delete();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data perawat berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus data perawat',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}