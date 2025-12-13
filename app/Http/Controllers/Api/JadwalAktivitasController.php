<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JadwalAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class JadwalAktivitasController extends Controller
{
    public function index()
{
    try {
        $jadwal = JadwalAktivitas::with(['datalansia', 'user', 'perawat'])
            ->orderBy('jam')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Semua data aktivitas berhasil diambil',
            'data' => $jadwal
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Gagal mengambil data aktivitas',
            'error' => $e->getMessage()
        ], 500);
    }
}



    /**
     * ALIAS untuk hariIni - untuk kompatibilitas dengan Flutter
     */
    public function getToday()
    {
        return $this->hariIni();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_aktivitas' => 'required|string|max:255',
            'jam' => 'required|date_format:H:i',
            'keterangan' => 'nullable|string',
            'hari' => 'nullable|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'status' => 'nullable|in:pending,completed',
            'completed' => 'nullable|boolean',
            'datalansia_id' => 'nullable|exists:datalansia,id',
            'perawat_id' => 'nullable|exists:users,id',
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
            
            $data = $request->all();
            $data['user_id'] = $user->id;
            
            // Jika tidak ada hari, default ke hari ini
            if (!isset($data['hari'])) {
                $data['hari'] = now()->locale('id')->dayName;
            }
            
            $jadwal = JadwalAktivitas::create($data);
            
            // Load relasi
            $jadwal->load(['datalansia', 'user', 'perawat']);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Jadwal berhasil ditambahkan',
                'data' => $jadwal
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menambahkan jadwal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $jadwal = JadwalAktivitas::with(['datalansia', 'user', 'perawat'])->find($id);
            
            if (!$jadwal) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Jadwal tidak ditemukan'
                ], 404);
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data jadwal berhasil diambil',
                'data' => $jadwal
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data jadwal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_aktivitas' => 'sometimes|string|max:255',
            'jam' => 'sometimes|date_format:H:i',
            'keterangan' => 'nullable|string',
            'hari' => 'sometimes|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'status' => 'sometimes|in:pending,completed',
            'completed' => 'sometimes|boolean',
            'datalansia_id' => 'nullable|exists:datalansia,id',
            'perawat_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $jadwal = JadwalAktivitas::find($id);
            
            if (!$jadwal) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Jadwal tidak ditemukan'
                ], 404);
            }
            
            // Update completed akan otomatis update status juga
            if ($request->has('completed')) {
                $request->merge([
                    'status' => $request->completed ? 'completed' : 'pending'
                ]);
            }
            
            $jadwal->update($request->all());
            
            // Refresh relasi
            $jadwal->load(['datalansia', 'user', 'perawat']);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Jadwal berhasil diperbarui',
                'data' => $jadwal
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui jadwal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateCompleted(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'completed' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $jadwal = JadwalAktivitas::find($id);
            
            if (!$jadwal) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Jadwal tidak ditemukan'
                ], 404);
            }
            
            $jadwal->update([
                'completed' => $request->completed,
                'status' => $request->completed ? 'completed' : 'pending'
            ]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Status jadwal berhasil diperbarui',
                'data' => $jadwal
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $jadwal = JadwalAktivitas::find($id);
            
            if (!$jadwal) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Jadwal tidak ditemukan'
                ], 404);
            }
            
            $jadwal->delete();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Jadwal berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus jadwal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Jadwal untuk hari ini
     */
    public function hariIni()
    {
        try {
            $user = Auth::user();
            $hari = now()->locale('id')->dayName;
            
            $query = JadwalAktivitas::with(['datalansia', 'user', 'perawat'])
                ->where('hari', $hari);
            
            // Filter berdasarkan role
            if ($user->role === 'keluarga') {
                $query->where('user_id', $user->id);
            } elseif ($user->role === 'perawat') {
                $query->where('perawat_id', $user->id)
                    ->orWhereNull('perawat_id');
            }
            
            $jadwal = $query->orderBy('jam')->get();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Jadwal hari ini berhasil diambil',
                'data' => $jadwal,
                'meta' => [
                    'hari' => $hari,
                    'total' => $jadwal->count(),
                    'selesai' => $jadwal->where('completed', true)->count(),
                    'belum_selesai' => $jadwal->where('completed', false)->count(),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil jadwal hari ini',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Jadwal untuk lansia tertentu
     */
    public function byLansia($datalansiaId)
    {
        try {
            $jadwal = JadwalAktivitas::with(['datalansia', 'user', 'perawat'])
                ->where('datalansia_id', $datalansiaId)
                ->orderBy('hari')
                ->orderBy('jam')
                ->get();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Jadwal lansia berhasil diambil',
                'data' => $jadwal
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil jadwal lansia',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}