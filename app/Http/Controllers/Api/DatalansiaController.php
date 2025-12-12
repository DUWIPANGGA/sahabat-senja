<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Datalansia;
use App\Models\Kamar;
use App\Models\User;
use App\Models\JadwalObat;
use App\Models\JadwalAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DatalansiaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index()
{
    $data = Datalansia::orderBy('nama_lansia')->get();

    return response()->json([
        'status' => 'success',
        'message' => 'Data lansia berhasil diambil',
        'data' => $data
    ], 200);
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
            'catatan_khusus' => 'nullable|string',
            'nama_anak' => 'required|string|max:100',
            'alamat_lengkap' => 'required|string|max:255',
            'no_hp_anak' => 'required|string|max:15',
            'email_anak' => 'required|string|email|max:100',
            'kontak_darurat_nama' => 'nullable|string|max:100',
            'kontak_darurat_hp' => 'nullable|string|max:15',
            'kontak_darurat_hubungan' => 'nullable|string|max:50',
            'kamar_id' => 'nullable|exists:kamar,id',
            'perawat_id' => 'nullable|exists:users,id',
            'jadwal_obat_rutin' => 'nullable|array',
            'jadwal_obat_rutin.*.nama_obat' => 'required_with:jadwal_obat_rutin|string',
            'jadwal_obat_rutin.*.waktu' => 'required_with:jadwal_obat_rutin|string',
            'jadwal_obat_rutin.*.dosis' => 'required_with:jadwal_obat_rutin|string',
            'jadwal_kegiatan_rutin' => 'nullable|array',
            'jadwal_kegiatan_rutin.*.nama_kegiatan' => 'required_with:jadwal_kegiatan_rutin|string',
            'jadwal_kegiatan_rutin.*.waktu' => 'required_with:jadwal_kegiatan_rutin|string',
            'jadwal_kegiatan_rutin.*.hari' => 'required_with:jadwal_kegiatan_rutin|string',
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
            
            // Prepare data
            $data = $request->all();
            
            // Remove no_kamar_lansia if exists (not in model)
            if (isset($data['no_kamar_lansia'])) {
                unset($data['no_kamar_lansia']);
            }
            
            // Set user_id based on role
            if ($user->role === 'keluarga') {
                $data['user_id'] = $user->id;
            } elseif ($user->role === 'admin') {
                // If admin, check if user exists for the email
                $keluargaUser = User::where('email', $request->email_anak)->first();
                if ($keluargaUser) {
                    $data['user_id'] = $keluargaUser->id;
                } else {
                    // If no user exists with this email, create a basic user
                    $keluargaUser = User::create([
                        'name' => $request->nama_anak,
                        'email' => $request->email_anak,
                        'password' => bcrypt('password123'), // Default password
                        'role' => 'keluarga',
                        'no_hp' => $request->no_hp_anak,
                    ]);
                    $data['user_id'] = $keluargaUser->id;
                }
            }
            
            // Set default values
            $data['tanggal_masuk'] = now()->toDateString();
            $data['status_lansia'] = 'aktif';
            
            // Handle JSON fields - map from request names to model field names
            if ($request->has('jadwal_obat_rutin')) {
                $data['obat_rutin_json'] = json_encode($request->jadwal_obat_rutin);
                unset($data['jadwal_obat_rutin']);
            }
            
            if ($request->has('jadwal_kegiatan_rutin')) {
                $data['jadwal_kegiatan_json'] = json_encode($request->jadwal_kegiatan_rutin);
                unset($data['jadwal_kegiatan_rutin']);
            }
            
            Log::info('Creating datalansia with data:', $data);
            
            // Create the data lansia
            $datalansia = Datalansia::create($data);
            
            // If kamar_id is provided, update kamar status
            if ($request->kamar_id) {
                $this->updateStatusKamar($request->kamar_id);
                
                // Generate jadwal rutin otomatis
                $this->generateJadwalRutin($datalansia);
            }
            
            // Load relations
            $datalansia->load(['kamar', 'user', 'kamar.perawat']);
            
            // Parse JSON fields for response
            $datalansia->jadwal_obat_rutin = json_decode($datalansia->obat_rutin_json ?? '[]', true);
            $datalansia->jadwal_kegiatan_rutin = json_decode($datalansia->jadwal_kegiatan_json ?? '[]', true);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data lansia berhasil ditambahkan',
                'data' => $datalansia
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating datalansia: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
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
            $user = Auth::user();
            $datalansia = Datalansia::with(['kamar', 'user', 'kamar.perawat'])->find($id);
            
            if (!$datalansia) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data lansia tidak ditemukan'
                ], 404);
            }
            
            // Cek akses berdasarkan role
            if ($user->role === 'keluarga' && $datalansia->user_id !== $user->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses ke data ini'
                ], 403);
            }
            
            if ($user->role === 'perawat') {
                $kamar = Kamar::where('perawat_id', $user->id)->first();
                if (!$kamar || $datalansia->kamar_id !== $kamar->id) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Anda tidak memiliki akses ke data ini'
                    ], 403);
                }
            }
            
            // Parse JSON fields - map from model field names to response names
            $datalansia->jadwal_obat_rutin = json_decode($datalansia->obat_rutin_json ?? '[]', true);
            $datalansia->jadwal_kegiatan_rutin = json_decode($datalansia->jadwal_kegiatan_json ?? '[]', true);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data lansia berhasil diambil',
                'data' => $datalansia
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error in show: ' . $e->getMessage());
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
            'catatan_khusus' => 'nullable|string',
            'nama_anak' => 'sometimes|string|max:100',
            'alamat_lengkap' => 'sometimes|string|max:255',
            'no_hp_anak' => 'sometimes|string|max:15',
            'email_anak' => 'sometimes|string|email|max:100',
            'kontak_darurat_nama' => 'nullable|string|max:100',
            'kontak_darurat_hp' => 'nullable|string|max:15',
            'kontak_darurat_hubungan' => 'nullable|string|max:50',
            'kamar_id' => 'nullable|exists:kamar,id',
            'perawat_id' => 'nullable|exists:users,id',
            'status_lansia' => 'nullable|in:aktif,pulang,meninggal',
            'jadwal_obat_rutin' => 'nullable|array',
            'jadwal_kegiatan_rutin' => 'nullable|array',
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
            
            $oldKamarId = $datalansia->kamar_id;
            $data = $request->all();
            
            // Remove no_kamar_lansia if exists
            if (isset($data['no_kamar_lansia'])) {
                unset($data['no_kamar_lansia']);
            }
            
            // Handle JSON fields
            if ($request->has('jadwal_obat_rutin')) {
                $data['obat_rutin_json'] = json_encode($request->jadwal_obat_rutin);
                unset($data['jadwal_obat_rutin']);
            }
            
            if ($request->has('jadwal_kegiatan_rutin')) {
                $data['jadwal_kegiatan_json'] = json_encode($request->jadwal_kegiatan_rutin);
                unset($data['jadwal_kegiatan_rutin']);
            }
            
            $datalansia->update($data);
            
            // Update status kamar jika ada perubahan kamar
            if ($request->has('kamar_id') && $request->kamar_id != $oldKamarId) {
                if ($oldKamarId) {
                    $this->updateStatusKamar($oldKamarId);
                }
                $this->updateStatusKamar($request->kamar_id);
            }
            
            // Jika status berubah menjadi non-aktif, update kamar
            if ($request->has('status_lansia') && in_array($request->status_lansia, ['pulang', 'meninggal'])) {
                if ($datalansia->kamar_id) {
                    $this->updateStatusKamar($datalansia->kamar_id);
                }
                
                // Set tanggal keluar
                if (!$datalansia->tanggal_keluar) {
                    $datalansia->update(['tanggal_keluar' => now()->toDateString()]);
                }
            }
            
            // Load relasi terbaru
            $datalansia->load(['kamar', 'user', 'kamar.perawat']);
            
            // Parse JSON fields for response
            $datalansia->jadwal_obat_rutin = json_decode($datalansia->obat_rutin_json ?? '[]', true);
            $datalansia->jadwal_kegiatan_rutin = json_decode($datalansia->jadwal_kegiatan_json ?? '[]', true);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data lansia berhasil diperbarui',
                'data' => $datalansia
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error in update: ' . $e->getMessage());
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
            
            // Simpan kamar_id sebelum dihapus
            $kamarId = $datalansia->kamar_id;
            
            $datalansia->delete();
            
            // Update status kamar
            if ($kamarId) {
                $this->updateStatusKamar($kamarId);
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data lansia berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error in destroy: ' . $e->getMessage());
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
            $datalansia = Datalansia::where('email_anak', $email)
                ->with(['kamar', 'kamar.perawat'])
                ->get();
            
            // Parse JSON fields
            $datalansia->transform(function ($item) {
                $item->jadwal_obat_rutin = json_decode($item->obat_rutin_json ?? '[]', true);
                $item->jadwal_kegiatan_rutin = json_decode($item->jadwal_kegiatan_json ?? '[]', true);
                return $item;
            });
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data lansia berhasil diambil',
                'data' => $datalansia
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error in getByKeluarga: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data lansia',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Pindahkan lansia ke kamar lain
     */
    public function pindahKamar(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'kamar_id_baru' => 'required|exists:kamar,id',
            'alasan' => 'nullable|string|max:255',
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
            
            $kamarLama = $datalansia->kamar;
            $kamarBaru = Kamar::find($request->kamar_id_baru);
            
            // Cek kapasitas kamar baru
            if ($kamarBaru->isFull()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kamar baru sudah penuh'
                ], 400);
            }
            
            // Update kamar lansia
            $datalansia->update([
                'kamar_id' => $kamarBaru->id,
                'catatan_khusus' => ($datalansia->catatan_khusus ?? '') . "\n[Pindah Kamar] " . 
                    date('Y-m-d H:i') . " - Dari: " . 
                    ($kamarLama ? $kamarLama->nomor_kamar : 'Tidak ada') . 
                    " ke: " . $kamarBaru->nomor_kamar . 
                    ($request->alasan ? " - Alasan: " . $request->alasan : '')
            ]);
            
            // Update status kamar lama dan baru
            if ($kamarLama) {
                $this->updateStatusKamar($kamarLama->id);
            }
            $this->updateStatusKamar($kamarBaru->id);
            
            // Parse JSON fields for response
            $datalansia->jadwal_obat_rutin = json_decode($datalansia->obat_rutin_json ?? '[]', true);
            $datalansia->jadwal_kegiatan_rutin = json_decode($datalansia->jadwal_kegiatan_json ?? '[]', true);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Lansia berhasil dipindahkan ke kamar ' . $kamarBaru->nomor_kamar,
                'data' => $datalansia->load(['kamar', 'kamar.perawat'])
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error in pindahKamar: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memindahkan kamar',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get data lansia by kamar
     */
    public function getByKamar($kamarId)
    {
        try {
            $kamar = Kamar::find($kamarId);
            
            if (!$kamar) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kamar tidak ditemukan'
                ], 404);
            }
            
            $datalansia = Datalansia::where('kamar_id', $kamarId)
                ->where('status_lansia', 'aktif')
                ->with(['kamar', 'user'])
                ->get();
            
            // Parse JSON fields
            $datalansia->transform(function ($item) {
                $item->jadwal_obat_rutin = json_decode($item->obat_rutin_json ?? '[]', true);
                $item->jadwal_kegiatan_rutin = json_decode($item->jadwal_kegiatan_json ?? '[]', true);
                return $item;
            });
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data lansia di kamar ' . $kamar->nomor_kamar . ' berhasil diambil',
                'data' => $datalansia,
                'kamar' => $kamar
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error in getByKamar: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data lansia',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get ringkasan data lansia untuk dashboard
     */
    public function ringkasan()
    {
        try {
            $user = Auth::user();
            
            if ($user->role === 'admin') {
                $data = [
                    'total_lansia' => Datalansia::count(),
                    'lansia_aktif' => Datalansia::where('status_lansia', 'aktif')->count(),
                    'lansia_pulang' => Datalansia::where('status_lansia', 'pulang')->count(),
                    'lansia_meninggal' => Datalansia::where('status_lansia', 'meninggal')->count(),
                    'kamar_terisi' => Datalansia::where('status_lansia', 'aktif')->distinct('kamar_id')->count('kamar_id'),
                    'kamar_kosong' => Kamar::where('status', 'tersedia')->count(),
                ];
            } elseif ($user->role === 'perawat') {
                $kamar = Kamar::where('perawat_id', $user->id)->first();
                
                if (!$kamar) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Tidak ada kamar yang ditugaskan',
                        'data' => []
                    ], 200);
                }
                
                $data = [
                    'kamar' => $kamar->nomor_kamar,
                    'total_lansia' => Datalansia::where('kamar_id', $kamar->id)->where('status_lansia', 'aktif')->count(),
                    'kapasitas_kamar' => $kamar->kapasitas,
                    'sisa_kapasitas' => $kamar->kapasitas - Datalansia::where('kamar_id', $kamar->id)->where('status_lansia', 'aktif')->count(),
                    'lansia_list' => Datalansia::where('kamar_id', $kamar->id)
                        ->where('status_lansia', 'aktif')
                        ->with(['kamar'])
                        ->get(['id', 'nama_lansia', 'umur_lansia', 'riwayat_penyakit_lansia'])
                ];
            } else {
                // Keluarga
                $data = [
                    'total_lansia' => Datalansia::where('user_id', $user->id)->count(),
                    'lansia_aktif' => Datalansia::where('user_id', $user->id)->where('status_lansia', 'aktif')->count(),
                    'lansia_list' => Datalansia::where('user_id', $user->id)
                        ->with(['kamar', 'kamar.perawat'])
                        ->get(['id', 'nama_lansia', 'kamar_id', 'status_lansia'])
                ];
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Ringkasan data lansia berhasil diambil',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error in ringkasan: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil ringkasan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update status kamar berdasarkan jumlah lansia
     */
    private function updateStatusKamar($kamarId)
    {
        if (!$kamarId) return; // biar aman kalau kamar_id NULL

        $kamar = Kamar::find($kamarId);

        if (!$kamar) return;

        // Hitung lansia aktif di kamar ini
        $jumlahLansia = Datalansia::where('kamar_id', $kamarId)
                                ->where('status_lansia', 'aktif')
                                ->count();

        // Update status kamar
        if ($jumlahLansia <= 0) {
            $kamar->status = 'tersedia';
        } elseif ($jumlahLansia >= $kamar->kapasitas) {
            $kamar->status = 'penuh';
        } else {
            $kamar->status = 'terisi';
        }

        $kamar->save();
        
        Log::info('Kamar ' . $kamar->nomor_kamar . ' status updated to: ' . $kamar->status);
    }
    
    /**
     * Generate jadwal rutin otomatis dari profil lansia
     */
    private function generateJadwalRutin($datalansia)
    {
        try {
            // Generate jadwal obat rutin from obat_rutin_json
            $jadwalObat = json_decode($datalansia->obat_rutin_json ?? '[]', true);
            
            foreach ($jadwalObat as $obat) {
                JadwalObat::create([
                    'datalansia_id' => $datalansia->id,
                    'nama_obat' => $obat['nama_obat'] ?? 'Obat Rutin',
                    'dosis' => $obat['dosis'] ?? '-',
                    'waktu' => $obat['waktu'] ?? 'Pagi',
                    'jenis' => 'rutin',
                    'frekuensi' => 'Setiap Hari',
                    'tanggal_mulai' => now()->toDateString(),
                    'auto_generate_tracking' => true,
                    'user_id' => Auth::id()
                ]);
            }
            
            // Generate jadwal kegiatan rutin from jadwal_kegiatan_json
            $jadwalKegiatan = json_decode($datalansia->jadwal_kegiatan_json ?? '[]', true);
            
            foreach ($jadwalKegiatan as $kegiatan) {
                JadwalAktivitas::create([
                    'datalansia_id' => $datalansia->id,
                    'nama_aktivitas' => $kegiatan['nama_kegiatan'] ?? 'Kegiatan Rutin',
                    'jam' => $kegiatan['waktu'] ?? '08:00',
                    'hari' => $kegiatan['hari'] ?? now()->locale('id')->dayName,
                    'jenis' => 'rutin',
                    'auto_generate' => true,
                    'user_id' => Auth::id()
                ]);
            }
            
            Log::info('Jadwal rutin generated for datalansia ID: ' . $datalansia->id);
        } catch (\Exception $e) {
            Log::error('Error generating jadwal rutin: ' . $e->getMessage());
        }
    }
    
    /**
     * Get data lansia dengan kondisi hari ini
     */
    public function denganKondisiHariIni()
    {
        try {
            $user = Auth::user();
            $today = now()->toDateString();
            
            $query = Datalansia::with(['kamar', 'kondisiHariIni']);
            
            // Filter berdasarkan role
            if ($user->role === 'perawat') {
                $kamar = Kamar::where('perawat_id', $user->id)->first();
                if ($kamar) {
                    $query->where('kamar_id', $kamar->id);
                }
            } elseif ($user->role === 'keluarga') {
                $query->where('user_id', $user->id);
            }
            
            $datalansia = $query->where('status_lansia', 'aktif')->get();
            
            // Parse JSON fields
            $datalansia->transform(function ($item) {
                $item->jadwal_obat_rutin = json_decode($item->obat_rutin_json ?? '[]', true);
                $item->jadwal_kegiatan_rutin = json_decode($item->jadwal_kegiatan_json ?? '[]', true);
                return $item;
            });
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data lansia dengan kondisi hari ini berhasil diambil',
                'data' => $datalansia,
                'meta' => [
                    'total' => $datalansia->count(),
                    'sudah_diperiksa' => $datalansia->where('kondisiHariIni', '!=', null)->count(),
                    'belum_diperiksa' => $datalansia->where('kondisiHariIni', null)->count(),
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error in denganKondisiHariIni: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function getAktifMonitoring()
    {
        try {
            $lansia = Datalansia::whereHas('kondisi', function($query) {
                $query->whereDate('tanggal', '>=', now()->subDays(7));
            })->get();
            
            // Parse JSON fields
            $lansia->transform(function ($item) {
                $item->jadwal_obat_rutin = json_decode($item->obat_rutin_json ?? '[]', true);
                $item->jadwal_kegiatan_rutin = json_decode($item->jadwal_kegiatan_json ?? '[]', true);
                return $item;
            });
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data lansia aktif monitoring berhasil diambil',
                'data' => $lansia
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error in getAktifMonitoring: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data lansia',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function search($keyword)
    {
        try {
            $lansia = Datalansia::where('nama_lansia', 'like', "%{$keyword}%")
                ->orWhere('alamat_lengkap', 'like', "%{$keyword}%")
                ->orWhere('email_anak', 'like', "%{$keyword}%")
                ->get();
            
            // Parse JSON fields
            $lansia->transform(function ($item) {
                $item->jadwal_obat_rutin = json_decode($item->obat_rutin_json ?? '[]', true);
                $item->jadwal_kegiatan_rutin = json_decode($item->jadwal_kegiatan_json ?? '[]', true);
                return $item;
            });
            
            return response()->json([
                'status' => 'success',
                'message' => 'Hasil pencarian berhasil diambil',
                'data' => $lansia
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error in search: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal melakukan pencarian',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}