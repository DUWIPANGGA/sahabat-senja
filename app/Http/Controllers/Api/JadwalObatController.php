<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JadwalObat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class JadwalObatController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = JadwalObat::with(['datalansia', 'user', 'perawat']);
            
            // Filter berdasarkan user role
            $user = Auth::user();
            
            if ($user->role === 'keluarga') {
                $query->where('user_id', $user->id);
            } elseif ($user->role === 'perawat') {
                $query->where('perawat_id', $user->id)
                    ->orWhereNull('perawat_id');
            }
            
            // Filter berdasarkan lansia
            if ($request->has('datalansia_id')) {
                $query->where('datalansia_id', $request->datalansia_id);
            }
            
            // Filter berdasarkan status selesai
            if ($request->has('selesai')) {
                $query->where('selesai', $request->boolean('selesai'));
            }
            
            // Filter jadwal aktif
            if ($request->boolean('aktif')) {
                $query->where('selesai', false)
                    ->where(function($q) {
                        $q->whereNull('tanggal_selesai')
                          ->orWhere('tanggal_selesai', '>=', now());
                    });
            }
            
            // Filter jadwal hari ini
            if ($request->boolean('hari_ini')) {
                $hariIni = now()->toDateString();
                $query->where('tanggal_mulai', '<=', $hariIni)
                    ->where(function($q) use ($hariIni) {
                        $q->whereNull('tanggal_selesai')
                          ->orWhere('tanggal_selesai', '>=', $hariIni);
                    });
            }
            
            // Filter berdasarkan waktu
            if ($request->has('waktu')) {
                $query->where('waktu', $request->waktu);
            }
            
            $jadwalObat = $query->orderBy('tanggal_mulai', 'desc')
                ->orderBy('waktu')
                ->get();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data jadwal obat berhasil diambil',
                'data' => $jadwalObat
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching jadwal obat: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data jadwal obat',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        Log::info('Creating new jadwal obat', $request->all());
        
        $validator = Validator::make($request->all(), [
            'datalansia_id' => 'required|exists:datalansia,id',
            'nama_obat' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'dosis' => 'required|string|max:100',
            'waktu' => 'required|in:Pagi,Siang,Sore,Malam,Sesuai Kebutuhan',
            'jam_minum' => 'nullable|date_format:H:i',
            'frekuensi' => 'required|in:Setiap Hari,Setiap 2 Hari,Mingguan,Bulanan,Sesuai Kebutuhan',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'catatan' => 'nullable|string',
            'perawat_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            Log::warning('Validation failed', $validator->errors()->toArray());
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
            
            // Hapus field selesai dari request (default false)
            unset($data['selesai']);
            
            // Konversi tanggal ke format yang benar
            if (isset($data['tanggal_mulai'])) {
                $data['tanggal_mulai'] = date('Y-m-d', strtotime($data['tanggal_mulai']));
            }
            
            if (isset($data['tanggal_selesai'])) {
                $data['tanggal_selesai'] = date('Y-m-d', strtotime($data['tanggal_selesai']));
            }
            
            // Log data yang akan disimpan
            Log::info('Data to save', $data);
            
            $jadwalObat = JadwalObat::create($data);
            
            // Load relasi
            $jadwalObat->load(['datalansia', 'user', 'perawat']);
            
            Log::info('Jadwal obat created successfully', ['id' => $jadwalObat->id]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Jadwal obat berhasil ditambahkan',
                'data' => $jadwalObat
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating jadwal obat: ' . $e->getMessage(), [
                'data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menambahkan jadwal obat',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $jadwalObat = JadwalObat::with(['datalansia', 'user', 'perawat'])->find($id);
            
            if (!$jadwalObat) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Jadwal obat tidak ditemukan'
                ], 404);
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data jadwal obat berhasil diambil',
                'data' => $jadwalObat
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data jadwal obat',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_obat' => 'sometimes|string|max:255',
            'deskripsi' => 'nullable|string',
            'dosis' => 'sometimes|string|max:100',
            'waktu' => 'sometimes|in:Pagi,Siang,Sore,Malam,Sesuai Kebutuhan',
            'jam_minum' => 'nullable|date_format:H:i',
            'frekuensi' => 'sometimes|in:Setiap Hari,Setiap 2 Hari,Mingguan,Bulanan,Sesuai Kebutuhan',
            'tanggal_mulai' => 'sometimes|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'selesai' => 'sometimes|boolean',
            'catatan' => 'nullable|string',
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
            $jadwalObat = JadwalObat::find($id);
            
            if (!$jadwalObat) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Jadwal obat tidak ditemukan'
                ], 404);
            }
            
            $jadwalObat->update($request->all());
            
            // Refresh relasi
            $jadwalObat->load(['datalansia', 'user', 'perawat']);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Jadwal obat berhasil diperbarui',
                'data' => $jadwalObat
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui jadwal obat',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateSelesai(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'selesai' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $jadwalObat = JadwalObat::find($id);
            
            if (!$jadwalObat) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Jadwal obat tidak ditemukan'
                ], 404);
            }
            
            $jadwalObat->update(['selesai' => $request->selesai]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Status jadwal obat berhasil diperbarui',
                'data' => $jadwalObat
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
            $jadwalObat = JadwalObat::find($id);
            
            if (!$jadwalObat) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Jadwal obat tidak ditemukan'
                ], 404);
            }
            
            $jadwalObat->delete();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Jadwal obat berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus jadwal obat',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Jadwal obat untuk lansia tertentu
     */
    public function byLansia($datalansiaId)
    {
        try {
            $jadwalObat = JadwalObat::with(['datalansia', 'user', 'perawat'])
                ->where('datalansia_id', $datalansiaId)
                ->orderBy('tanggal_mulai', 'desc')
                ->orderBy('waktu')
                ->get();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Jadwal obat lansia berhasil diambil',
                'data' => $jadwalObat
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil jadwal obat lansia',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Jadwal obat aktif untuk hari ini
     */
    public function aktifHariIni()
    {
        try {
            $user = Auth::user();
            $hariIni = now()->toDateString();
            
            $query = JadwalObat::with(['datalansia', 'user', 'perawat'])
                ->where('selesai', false)
                ->where('tanggal_mulai', '<=', $hariIni)
                ->where(function($q) use ($hariIni) {
                    $q->whereNull('tanggal_selesai')
                      ->orWhere('tanggal_selesai', '>=', $hariIni);
                });
            
            // Filter berdasarkan role
            if ($user->role === 'keluarga') {
                $query->where('user_id', $user->id);
            } elseif ($user->role === 'perawat') {
                $query->where('perawat_id', $user->id)
                    ->orWhereNull('perawat_id');
            }
            
            $jadwalObat = $query->orderBy('waktu')
                ->orderBy('jam_minum')
                ->get();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Jadwal obat aktif hari ini berhasil diambil',
                'data' => $jadwalObat,
                'meta' => [
                    'total' => $jadwalObat->count(),
                    'hari' => now()->format('d/m/Y'),
                    'pagi' => $jadwalObat->where('waktu', 'Pagi')->count(),
                    'siang' => $jadwalObat->where('waktu', 'Siang')->count(),
                    'sore' => $jadwalObat->where('waktu', 'Sore')->count(),
                    'malam' => $jadwalObat->where('waktu', 'Malam')->count(),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil jadwal obat hari ini',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update jam minum (untuk menandai sudah diminum)
     */
    public function updateJamMinum(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'jam_minum' => 'required|date_format:H:i',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $jadwalObat = JadwalObat::find($id);
            
            if (!$jadwalObat) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Jadwal obat tidak ditemukan'
                ], 404);
            }
            
            $jadwalObat->update([
                'jam_minum' => $request->jam_minum,
                'selesai' => false, // Reset selesai jika sudah diminum
            ]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Jam minum berhasil diperbarui',
                'data' => $jadwalObat
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui jam minum',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}