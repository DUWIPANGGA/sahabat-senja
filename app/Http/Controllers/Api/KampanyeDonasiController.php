<?php

namespace App\Http\Controllers\Api;

use Log;
use App\Models\Datalansia;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\KampanyeDonasi;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class KampanyeDonasiController extends Controller
{
    /**
     * Display a listing of kampanye
     */
    public function index(Request $request)
    {
        try {
            $query = KampanyeDonasi::with(['datalansia' => function($q) {
                $q->select('id', 'nama_lansia');
            }]);

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            } else {
                $query->where('status', 'aktif');
            }

            // Filter by kategori
            if ($request->has('kategori')) {
                $query->where('kategori', $request->kategori);
            }

            // Filter by featured
            if ($request->has('is_featured')) {
                $query->where('is_featured', $request->is_featured == 'true');
            }

            // Search by judul
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('judul', 'like', "%{$search}%")
                      ->orWhere('deskripsi_singkat', 'like', "%{$search}%");
                });
            }

            // Sort
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 10);
            $kampanyes = $query->paginate($perPage);

            // Transform data
            $data = $kampanyes->map(function ($kampanye) {
                return $this->transformKampanye($kampanye);
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Data kampanye berhasil diambil',
                'data' => $data,
                'meta' => [
                    'current_page' => $kampanyes->currentPage(),
                    'last_page' => $kampanyes->lastPage(),
                    'per_page' => $kampanyes->perPage(),
                    'total' => $kampanyes->total(),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data kampanye',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get active kampanye
     */
    public function active(Request $request)
    {
        try {
            $query = KampanyeDonasi::aktif()
                ->with(['datalansia' => function($q) {
                    $q->select('id', 'nama_lansia');
                }]);

            // Filter by kategori
            if ($request->has('kategori')) {
                $query->where('kategori', $request->kategori);
            }

            // Sort by priority
            $query->orderBy('is_featured', 'desc')
                  ->orderBy('jumlah_donatur', 'desc')
                  ->orderBy('created_at', 'desc');

            $perPage = $request->get('per_page', 10);
            $kampanyes = $query->paginate($perPage);

            $data = $kampanyes->map(function ($kampanye) {
                return $this->transformKampanye($kampanye);
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Data kampanye aktif berhasil diambil',
                'data' => $data,
                'meta' => $kampanyes->toArray()
            ]);

        } catch (\Exception $e) {
                            Log::error('Error transforming kampanye: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data kampanye aktif',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get featured kampanye
     */
    public function featured()
    {
        try {
            $kampanyes = KampanyeDonasi::aktif()
                ->featured()
                ->with(['datalansia' => function($q) {
                    $q->select('id', 'nama_lansia');
                }])
                ->limit(5)
                ->get();

            $data = $kampanyes->map(function ($kampanye) {
                return $this->transformKampanye($kampanye);
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Data kampanye featured berhasil diambil',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data kampanye featured',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get kampanye by category
     */
    public function byCategory($category)
    {
        try {
            $kampanyes = KampanyeDonasi::aktif()
                ->where('kategori', $category)
                ->with(['datalansia' => function($q) {
                    $q->select('id', 'nama_lansia');
                }])
                ->orderBy('is_featured', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            $data = $kampanyes->map(function ($kampanye) {
                return $this->transformKampanye($kampanye);
            });

            return response()->json([
                'status' => 'success',
                'message' => "Data kampanye kategori {$category} berhasil diambil",
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data kampanye',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all categories
     */
    public function categories()
    {
        try {
            $categories = KampanyeDonasi::aktif()
                ->select('kategori')
                ->distinct()
                ->pluck('kategori');

            return response()->json([
                'status' => 'success',
                'message' => 'Data kategori berhasil diambil',
                'data' => $categories
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data kategori',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified kampanye
     */
    public function show($slug)
    {
        try {
            $kampanye = KampanyeDonasi::with(['datalansia' => function($q) {
                    $q->select('id', 'nama_lansia', 'umur_lansia', 'alamat_lengkap');
                }])
                ->where('slug', $slug)
                ->first();

            if (!$kampanye) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kampanye tidak ditemukan'
                ], 404);
            }

            // Increment view count
            $kampanye->increment('jumlah_dilihat');

            // Get recent donations for this campaign
            $recentDonations = \App\Models\Donasi::where('kampanye_donasi_id', $kampanye->id)
                ->where('status', 'sukses')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($donasi) {
                    return [
                        'nama' => $donasi->anonim ? 'Anonim' : $donasi->nama_donatur,
                        'jumlah' => 'Rp ' . number_format($donasi->jumlah, 0, ',', '.'),
                        'waktu' => $donasi->created_at->diffForHumans(),
                        'doa_harapan' => $donasi->doa_harapan,
                    ];
                });

            // Get similar campaigns
            $similarCampaigns = KampanyeDonasi::aktif()
                ->where('kategori', $kampanye->kategori)
                ->where('id', '!=', $kampanye->id)
                ->with(['datalansia' => function($q) {
                    $q->select('id', 'nama_lansia');
                }])
                ->limit(4)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'judul' => $item->judul,
                        'slug' => $item->slug,
                        'deskripsi_singkat' => $item->deskripsi_singkat,
                        'gambar' => $item->gambar ? url('storage/' . $item->gambar) : null,
                        'target_dana' => 'Rp ' . number_format($item->target_dana, 0, ',', '.'),
                        'dana_terkumpul' => 'Rp ' . number_format($item->dana_terkumpul, 0, ',', '.'),
                        'progress' => $item->progress,
                        'hari_tersisa' => $item->hari_tersisa,
                        'jumlah_donatur' => $item->jumlah_donatur,
                        'datalansia' => $item->datalansia ? [
                            'nama' => $item->datalansia->nama_lansia,
                        ] : null,
                    ];
                });

            $data = $this->transformKampanye($kampanye, true);
            $data['recent_donations'] = $recentDonations;
            $data['similar_campaigns'] = $similarCampaigns;
            $data['gallery'] = $kampanye->galeri ? array_map(function($image) {
                return url('storage/' . $image);
            }, $kampanye->galeri) : [];

            return response()->json([
                'status' => 'success',
                'message' => 'Data kampanye berhasil diambil',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('Error show: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data kampanye',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get kampanye statistics
     */
    public function statistics()
    {
        try {
            $totalKampanye = KampanyeDonasi::aktif()->count();
            $totalDonasi = KampanyeDonasi::aktif()->sum('dana_terkumpul');
            $totalDonatur = KampanyeDonasi::aktif()->sum('jumlah_donatur');
            $kampanyeSelesai = KampanyeDonasi::where('status', 'selesai')->count();

            return response()->json([
                'status' => 'success',
                'message' => 'Statistik kampanye berhasil diambil',
                'data' => [
                    'total_kampanye' => $totalKampanye,
                    'total_donasi' => 'Rp ' . number_format($totalDonasi, 0, ',', '.'),
                    'total_donatur' => $totalDonatur,
                    'kampanye_selesai' => $kampanyeSelesai,
                    'kampanye_aktif' => $totalKampanye,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil statistik',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get trending campaigns
     */
    public function trending()
    {
        try {
            $kampanyes = KampanyeDonasi::aktif()
                ->with(['datalansia' => function($q) {
                    $q->select('id', 'nama_lansia');
                }])
                ->orderBy('jumlah_dilihat', 'desc')
                ->orderBy('jumlah_donatur', 'desc')
                ->limit(6)
                ->get();

            $data = $kampanyes->map(function ($kampanye) {
                return $this->transformKampanye($kampanye);
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Data kampanye trending berhasil diambil',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data trending',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get kampanye for elderly
     */
    public function forElderly($datalansiaId)
    {
        try {
            $kampanyes = KampanyeDonasi::aktif()
                ->where('datalansia_id', $datalansiaId)
                ->with(['datalansia' => function($q) {
                    $q->select('id', 'nama_lansia');
                }])
                ->orderBy('is_featured', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            $data = $kampanyes->map(function ($kampanye) {
                return $this->transformKampanye($kampanye);
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Data kampanye untuk lansia berhasil diambil',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data kampanye lansia',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created kampanye
     */
    public function store(Request $request)
    {
        // Hanya admin yang bisa membuat kampanye
        if (!auth()->user() || !auth()->user()->isAdmin()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'deskripsi_singkat' => 'required|string|max:500',
            'target_dana' => 'required|numeric|min:100000',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'kategori' => 'required|string',
            'datalansia_id' => 'nullable|exists:datalansia,id',
            'cerita_lengkap' => 'nullable|string',
            'is_featured' => 'boolean',
            'galeri.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->except(['gambar', 'thumbnail', 'galeri']);
            $data['slug'] = Str::slug($request->judul) . '-' . Str::random(6);
            $data['status'] = 'aktif';
            $data['dana_terkumpul'] = 0;
            $data['jumlah_donatur'] = 0;
            $data['jumlah_dilihat'] = 0;

            // Upload gambar utama
            if ($request->hasFile('gambar')) {
                $path = $request->file('gambar')->store('kampanye', 'public');
                $data['gambar'] = $path;
            }

            // Upload thumbnail
            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->store('kampanye/thumbnails', 'public');
                $data['thumbnail'] = $thumbnailPath;
            }

            // Upload galeri
            if ($request->hasFile('galeri')) {
                $galeri = [];
                foreach ($request->file('galeri') as $image) {
                    $path = $image->store('kampanye/galeri', 'public');
                    $galeri[] = $path;
                }
                $data['galeri'] = $galeri;
            }

            $kampanye = KampanyeDonasi::create($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Kampanye berhasil dibuat',
                'data' => $this->transformKampanye($kampanye)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membuat kampanye',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified kampanye
     */
    public function update(Request $request, $id)
    {
        // Hanya admin yang bisa update
        if (!auth()->user() || !auth()->user()->isAdmin()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }

        $kampanye = KampanyeDonasi::find($id);
        if (!$kampanye) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kampanye tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'judul' => 'sometimes|string|max:255',
            'deskripsi' => 'sometimes|string',
            'deskripsi_singkat' => 'sometimes|string|max:500',
            'target_dana' => 'sometimes|numeric|min:100000',
            'tanggal_mulai' => 'sometimes|date',
            'tanggal_selesai' => 'sometimes|date|after:tanggal_mulai',
            'gambar' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:5120',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'kategori' => 'sometimes|string',
            'datalansia_id' => 'nullable|exists:datalansia,id',
            'cerita_lengkap' => 'nullable|string',
            'status' => 'sometimes|in:aktif,nonaktif,selesai',
            'is_featured' => 'sometimes|boolean',
            'galeri.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->except(['gambar', 'thumbnail', 'galeri']);

            // Update slug jika judul berubah
            if ($request->has('judul')) {
                $data['slug'] = Str::slug($request->judul) . '-' . Str::random(6);
            }

            // Update gambar utama
            if ($request->hasFile('gambar')) {
                // Hapus gambar lama
                if ($kampanye->gambar && Storage::disk('public')->exists($kampanye->gambar)) {
                    Storage::disk('public')->delete($kampanye->gambar);
                }
                $path = $request->file('gambar')->store('kampanye', 'public');
                $data['gambar'] = $path;
            }

            // Update thumbnail
            if ($request->hasFile('thumbnail')) {
                // Hapus thumbnail lama
                if ($kampanye->thumbnail && Storage::disk('public')->exists($kampanye->thumbnail)) {
                    Storage::disk('public')->delete($kampanye->thumbnail);
                }
                $thumbnailPath = $request->file('thumbnail')->store('kampanye/thumbnails', 'public');
                $data['thumbnail'] = $thumbnailPath;
            }

            // Update galeri
            if ($request->hasFile('galeri')) {
                // Hapus galeri lama
                if ($kampanye->galeri) {
                    foreach ($kampanye->galeri as $oldImage) {
                        if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                            Storage::disk('public')->delete($oldImage);
                        }
                    }
                }
                
                $galeri = [];
                foreach ($request->file('galeri') as $image) {
                    $path = $image->store('kampanye/galeri', 'public');
                    $galeri[] = $path;
                }
                $data['galeri'] = $galeri;
            }

            $kampanye->update($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Kampanye berhasil diupdate',
                'data' => $this->transformKampanye($kampanye)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengupdate kampanye',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete kampanye
     */
    public function destroy($id)
    {
        // Hanya admin yang bisa delete
        if (!auth()->user() || !auth()->user()->isAdmin()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }

        try {
            $kampanye = KampanyeDonasi::find($id);
            
            if (!$kampanye) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kampanye tidak ditemukan'
                ], 404);
            }

            // Soft delete
            $kampanye->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Kampanye berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus kampanye',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Transform kampanye data for API response
     */
    private function transformKampanye($kampanye, $detail = false)
    {
        $data = [
            'id' => $kampanye->id,
            'judul' => $kampanye->judul,
            'slug' => $kampanye->slug,
            'deskripsi_singkat' => $kampanye->deskripsi_singkat,
            'gambar' => $kampanye->gambar ? url('storage/' . $kampanye->gambar) : null,
            'thumbnail' => $kampanye->thumbnail ? url('storage/' . $kampanye->thumbnail) : null,
            'target_dana' => 'Rp ' . number_format($kampanye->target_dana, 0, ',', '.'),
            'dana_terkumpul' => 'Rp ' . number_format($kampanye->dana_terkumpul, 0, ',', '.'),
            'progress' => $kampanye->progress,
            'hari_tersisa' => $kampanye->hari_tersisa,
            'is_active' => $kampanye->is_active,
            'kategori' => $kampanye->kategori,
            'status' => $kampanye->status,
            'is_featured' => $kampanye->is_featured,
            'jumlah_donatur' => $kampanye->jumlah_donatur,
            'jumlah_dilihat' => $kampanye->jumlah_dilihat,
            'tanggal_mulai' => $kampanye->tanggal_mulai->format('d M Y'),
            'tanggal_selesai' => $kampanye->tanggal_selesai->format('d M Y'),
            'created_at' => $kampanye->created_at->format('d M Y'),
            'terima_kasih_pesan' => $kampanye->terima_kasih_pesan,
            'datalansia' => $kampanye->datalansia ? [
                'id' => $kampanye->datalansia->id,
                'nama' => $kampanye->datalansia->nama_lansia,
                'foto' => $kampanye->datalansia->foto ? url('storage/' . $kampanye->datalansia->foto) : null,
                'umur' => $kampanye->datalansia->umur_lansia,
                'alamat' => $kampanye->datalansia->alamat_lengkap,
            ] : null,
        ];

        if ($detail) {
            $data['deskripsi'] = $kampanye->deskripsi;
            $data['cerita_lengkap'] = $kampanye->cerita_lengkap;
            $data['meta_title'] = $kampanye->meta_title;
            $data['meta_description'] = $kampanye->meta_description;
            $data['galeri'] = $kampanye->galeri ? array_map(function($image) {
                return url('storage/' . $image);
            }, $kampanye->galeri) : [];
        }

        return $data;
    }
}