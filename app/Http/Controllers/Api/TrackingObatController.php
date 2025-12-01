<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TrackingObat;
use App\Models\JadwalObat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TrackingObatController extends Controller
{
    /**
     * Get all tracking obat
     */
    public function index(Request $request)
    {
        try {
            $query = TrackingObat::with(['datalansia', 'perawat']);
            
            // Filter berdasarkan perawat yang login
            $user = Auth::user();
            if ($user->role === 'perawat') {
                $query->where('perawat_id', $user->id)
                    ->orWhereNull('perawat_id');
            }
            
            // Filter berdasarkan tanggal
            if ($request->has('tanggal')) {
                $query->whereDate('tanggal', $request->tanggal);
            }
            
            // Filter berdasarkan status
            if ($request->has('sudah_diberikan')) {
                $query->where('sudah_diberikan', $request->boolean('sudah_diberikan'));
            }
            
            // Filter berdasarkan lansia
            if ($request->has('datalansia_id')) {
                $query->where('datalansia_id', $request->datalansia_id);
            }
            
            // Filter berdasarkan waktu (Pagi, Siang, Sore, Malam)
            if ($request->has('waktu')) {
                $query->where('waktu', $request->waktu);
            }
            
            // Sort by waktu dan tanggal
            $tracking = $query->orderBy('tanggal', 'desc')
                ->orderByRaw("FIELD(waktu, 'Pagi', 'Siang', 'Sore', 'Malam')")
                ->get();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data tracking obat berhasil diambil',
                'data' => $tracking
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching tracking obat: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data tracking obat',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get tracking by tanggal
     */
    public function byTanggal($tanggal)
    {
        try {
            $query = TrackingObat::with(['datalansia', 'perawat'])
                ->whereDate('tanggal', $tanggal);
            
            // Filter berdasarkan perawat yang login
            $user = Auth::user();
            if ($user->role === 'perawat') {
                $query->where('perawat_id', $user->id)
                    ->orWhereNull('perawat_id');
            }
            
            $tracking = $query->orderByRaw("FIELD(waktu, 'Pagi', 'Siang', 'Sore', 'Malam')")
                ->get();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data tracking obat berhasil diambil',
                'data' => $tracking,
                'meta' => [
                    'tanggal' => $tanggal,
                    'total' => $tracking->count(),
                    'sudah_diberikan' => $tracking->where('sudah_diberikan', true)->count(),
                    'belum_diberikan' => $tracking->where('sudah_diberikan', false)->count(),
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching tracking by tanggal: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data tracking obat',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get tracking hari ini
     */
    public function hariIni()
    {
        try {
            $hariIni = Carbon::now()->toDateString();
            
            $query = TrackingObat::with(['datalansia', 'perawat'])
                ->whereDate('tanggal', $hariIni);
            
            // Filter berdasarkan perawat yang login
            $user = Auth::user();
            if ($user->role === 'perawat') {
                $query->where('perawat_id', $user->id)
                    ->orWhereNull('perawat_id');
            }
            
            $tracking = $query->orderByRaw("FIELD(waktu, 'Pagi', 'Siang', 'Sore', 'Malam')")
                ->get();
            
            $total = $tracking->count();
            $sudah = $tracking->where('sudah_diberikan', true)->count();
            $belum = $total - $sudah;
            
            // Hitung yang terlambat
            $terlambat = $tracking->filter(function($item) {
                if ($item->sudah_diberikan) return false;
                
                $waktuMapping = [
                    'Pagi' => 9,
                    'Siang' => 13,
                    'Sore' => 17,
                    'Malam' => 21,
                ];
                
                $targetHour = $waktuMapping[$item->waktu] ?? 12;
                return Carbon::now()->hour > $targetHour;
            })->count();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data tracking obat hari ini berhasil diambil',
                'data' => $tracking,
                'meta' => [
                    'tanggal' => $hariIni,
                    'total' => $total,
                    'sudah_diberikan' => $sudah,
                    'belum_diberikan' => $belum,
                    'terlambat' => $terlambat,
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching tracking hari ini: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data tracking obat hari ini',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get tracking by lansia
     */
    public function byLansia($datalansiaId)
    {
        try {
            $tracking = TrackingObat::with(['datalansia', 'perawat'])
                ->where('datalansia_id', $datalansiaId)
                ->orderBy('tanggal', 'desc')
                ->orderByRaw("FIELD(waktu, 'Pagi', 'Siang', 'Sore', 'Malam')")
                ->get();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data tracking obat lansia berhasil diambil',
                'data' => $tracking
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching tracking by lansia: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data tracking obat lansia',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Store new tracking obat
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jadwal_obat_id' => 'required|exists:jadwal_obat,id',
            'datalansia_id' => 'required|exists:datalansia,id',
            'nama_obat' => 'required|string|max:255',
            'dosis' => 'required|string|max:100',
            'waktu' => 'required|in:Pagi,Siang,Sore,Malam',
            'tanggal' => 'required|date',
            'jam_pemberian' => 'nullable|date_format:H:i',
            'sudah_diberikan' => 'boolean',
            'catatan' => 'nullable|string',
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
            $data['perawat_id'] = $user->role === 'perawat' ? $user->id : null;
            
            $tracking = TrackingObat::create($data);
            
            // Load relasi
            $tracking->load(['datalansia', 'perawat']);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Tracking obat berhasil ditambahkan',
                'data' => $tracking
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating tracking obat: ' . $e->getMessage(), [
                'data' => $request->all()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menambahkan tracking obat',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Show tracking obat
     */
    public function show($id)
    {
        try {
            $tracking = TrackingObat::with(['datalansia', 'perawat'])->find($id);
            
            if (!$tracking) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tracking obat tidak ditemukan'
                ], 404);
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data tracking obat berhasil diambil',
                'data' => $tracking
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching tracking obat: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data tracking obat',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Update status pemberian obat
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'sudah_diberikan' => 'required|boolean',
            'jam_pemberian' => 'nullable|date_format:H:i',
            'catatan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $tracking = TrackingObat::find($id);
            
            if (!$tracking) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tracking obat tidak ditemukan'
                ], 404);
            }
            
            $data = $request->all();
            
            // Jika sudah diberikan, set jam pemberian ke waktu sekarang
            if ($request->sudah_diberikan && !$request->has('jam_pemberian')) {
                $data['jam_pemberian'] = Carbon::now()->format('H:i');
            }
            
            // Jika belum diberikan, hapus jam pemberian
            if (!$request->sudah_diberikan) {
                $data['jam_pemberian'] = null;
            }
            
            $tracking->update($data);
            
            // Load relasi terbaru
            $tracking->load(['datalansia', 'perawat']);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Status tracking obat berhasil diperbarui',
                'data' => $tracking
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error updating tracking status: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui status tracking',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Update catatan
     */
    public function updateCatatan(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'catatan' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $tracking = TrackingObat::find($id);
            
            if (!$tracking) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tracking obat tidak ditemukan'
                ], 404);
            }
            
            $tracking->update(['catatan' => $request->catatan]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Catatan tracking obat berhasil diperbarui',
                'data' => $tracking
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error updating tracking catatan: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui catatan tracking',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Delete tracking obat
     */
    public function destroy($id)
    {
        try {
            $tracking = TrackingObat::find($id);
            
            if (!$tracking) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tracking obat tidak ditemukan'
                ], 404);
            }
            
            $tracking->delete();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Tracking obat berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting tracking obat: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus tracking obat',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Generate tracking untuk hari ini dari jadwal obat aktif
     */
    public function generateHariIni()
    {
        try {
            $hariIni = Carbon::now()->toDateString();
            $user = Auth::user();
            
            // Ambil semua jadwal obat aktif untuk hari ini
            $jadwalObatAktif = JadwalObat::where('selesai', false)
                ->whereDate('tanggal_mulai', '<=', $hariIni)
                ->where(function($query) use ($hariIni) {
                    $query->whereNull('tanggal_selesai')
                          ->orWhere('tanggal_selesai', '>=', $hariIni);
                })
                ->get();
            
            $generatedCount = 0;
            $errors = [];
            
            foreach ($jadwalObatAktif as $jadwal) {
                try {
                    // Cek apakah sudah ada tracking untuk hari ini
                    $existingTracking = TrackingObat::where('jadwal_obat_id', $jadwal->id)
                        ->whereDate('tanggal', $hariIni)
                        ->first();
                    
                    if (!$existingTracking) {
                        // Buat tracking baru
                        TrackingObat::create([
                            'jadwal_obat_id' => $jadwal->id,
                            'datalansia_id' => $jadwal->datalansia_id,
                            'nama_obat' => $jadwal->nama_obat,
                            'dosis' => $jadwal->dosis,
                            'waktu' => $jadwal->waktu,
                            'tanggal' => $hariIni,
                            'perawat_id' => $user->role === 'perawat' ? $user->id : null,
                        ]);
                        
                        $generatedCount++;
                    }
                } catch (\Exception $e) {
                    $errors[] = [
                        'jadwal_id' => $jadwal->id,
                        'error' => $e->getMessage()
                    ];
                    Log::error('Error generating tracking for jadwal ' . $jadwal->id . ': ' . $e->getMessage());
                }
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Tracking obat hari ini berhasil digenerate',
                'data' => [
                    'generated' => $generatedCount,
                    'total_jadwal' => $jadwalObatAktif->count(),
                    'errors' => $errors,
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error generating tracking hari ini: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal generate tracking hari ini',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get statistics for dashboard
     */
    public function statistics(Request $request)
    {
        try {
            $tanggal = $request->get('tanggal', Carbon::now()->toDateString());
            $user = Auth::user();
            
            $query = TrackingObat::whereDate('tanggal', $tanggal);
            
            // Filter berdasarkan perawat
            if ($user->role === 'perawat') {
                $query->where('perawat_id', $user->id)
                    ->orWhereNull('perawat_id');
            }
            
            $tracking = $query->get();
            
            $total = $tracking->count();
            $sudah = $tracking->where('sudah_diberikan', true)->count();
            $belum = $total - $sudah;
            
            // Hitung per waktu
            $perWaktu = [
                'Pagi' => $tracking->where('waktu', 'Pagi')->count(),
                'Siang' => $tracking->where('waktu', 'Siang')->count(),
                'Sore' => $tracking->where('waktu', 'Sore')->count(),
                'Malam' => $tracking->where('waktu', 'Malam')->count(),
            ];
            
            // Hitung terlambat
            $terlambat = $tracking->filter(function($item) {
                if ($item->sudah_diberikan) return false;
                
                $waktuMapping = [
                    'Pagi' => 9,
                    'Siang' => 13,
                    'Sore' => 17,
                    'Malam' => 21,
                ];
                
                $targetHour = $waktuMapping[$item->waktu] ?? 12;
                return Carbon::now()->hour > $targetHour;
            })->count();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Statistik tracking obat berhasil diambil',
                'data' => [
                    'total' => $total,
                    'sudah_diberikan' => $sudah,
                    'belum_diberikan' => $belum,
                    'terlambat' => $terlambat,
                    'per_waktu' => $perWaktu,
                    'tanggal' => $tanggal,
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching tracking statistics: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil statistik tracking',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}