<?php
// app/Http/Controllers/Api/KamarController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kamar;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KamarController extends Controller
{
    public function index()
    {
        try {
            $kamar = Kamar::with(['perawat', 'lansia'])->get();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data kamar berhasil diambil',
                'data' => $kamar
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data kamar',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function assignPerawat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kamar_id' => 'required|exists:kamar,id',
            'perawat_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $kamar = Kamar::find($request->kamar_id);
            $perawat = User::find($request->perawat_id);
            
            // Cek role perawat
            if ($perawat->role !== 'perawat') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User bukan perawat'
                ], 400);
            }
            
            $kamar->perawat_id = $perawat->id;
            $kamar->save();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Perawat berhasil ditugaskan ke kamar',
                'data' => $kamar->load('perawat')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menugaskan perawat',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getLansiaByKamar($kamarId)
    {
        try {
            $kamar = Kamar::with('lansia')->find($kamarId);
            
            if (!$kamar) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kamar tidak ditemukan'
                ], 404);
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data lansia di kamar berhasil diambil',
                'data' => $kamar->lansia
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