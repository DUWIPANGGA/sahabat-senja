<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dataperawat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DataperawatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $dataperawat = Dataperawat::all();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data perawat berhasil diambil',
                'data' => $dataperawat
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
            'email' => 'required|string|email|max:100|unique:dataperawat',
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
            $dataperawat = Dataperawat::create($request->all());
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data perawat berhasil ditambahkan',
                'data' => $dataperawat
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
            $dataperawat = Dataperawat::find($id);
            
            if (!$dataperawat) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data perawat tidak ditemukan'
                ], 404);
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data perawat berhasil diambil',
                'data' => $dataperawat
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
            'email' => 'sometimes|string|email|max:100|unique:dataperawat,email,' . $id,
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
            $dataperawat = Dataperawat::find($id);
            
            if (!$dataperawat) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data perawat tidak ditemukan'
                ], 404);
            }
            
            $dataperawat->update($request->all());
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data perawat berhasil diperbarui',
                'data' => $dataperawat
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
            $dataperawat = Dataperawat::find($id);
            
            if (!$dataperawat) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data perawat tidak ditemukan'
                ], 404);
            }
            
            $dataperawat->delete();
            
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