<?php

namespace App\Http\Controllers;

use App\Models\Datalansia;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DatalansiaExport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DatalansiaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Ambil nilai keyword dari input pencarian (GET)
        $keyword = $request->get('search');
        $jenisKelamin = $request->get('jenis_kelamin');
        $sort = $request->get('sort', 'terbaru');

        // Query untuk data utama
        $query = Datalansia::query();
        
        // Filter pencarian
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('nama_lansia', 'like', "%{$keyword}%")
                  ->orWhere('nama_anak', 'like', "%{$keyword}%")
                  ->orWhere('no_hp_anak', 'like', "%{$keyword}%")
                  ->orWhere('email_anak', 'like', "%{$keyword}%");
            });
        }

        // Filter jenis kelamin
        if ($jenisKelamin) {
            $query->where('jenis_kelamin_lansia', $jenisKelamin);
        }

        // Sorting
        switch ($sort) {
            case 'nama_asc':
                $query->orderBy('nama_lansia', 'asc');
                break;
            case 'nama_desc':
                $query->orderBy('nama_lansia', 'desc');
                break;
            case 'umur_asc':
                $query->orderBy('umur_lansia', 'asc');
                break;
            case 'umur_desc':
                $query->orderBy('umur_lansia', 'desc');
                break;
            case 'terbaru':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // Pagination dengan 10 data per halaman
        $datalansia = $query->paginate(10)->appends([
            'search' => $keyword,
            'jenis_kelamin' => $jenisKelamin,
            'sort' => $sort
        ]);

        // Cache statistik selama 5 menit untuk performa lebih baik
        $cacheKey = 'stats_datalansia_' . md5(($keyword ?? '') . ($jenisKelamin ?? ''));
        
        $stats = Cache::remember($cacheKey, 300, function () use ($keyword, $jenisKelamin) {
            $query = Datalansia::query();
            
            if ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('nama_lansia', 'like', "%{$keyword}%")
                      ->orWhere('nama_anak', 'like', "%{$keyword}%")
                      ->orWhere('no_hp_anak', 'like', "%{$keyword}%")
                      ->orWhere('email_anak', 'like', "%{$keyword}%");
                });
            }
            
            if ($jenisKelamin) {
                $query->where('jenis_kelamin_lansia', $jenisKelamin);
            }
            
            $lakiLaki = $query->clone()->where('jenis_kelamin_lansia', 'Laki-laki')->count();
            $perempuan = $query->clone()->where('jenis_kelamin_lansia', 'Perempuan')->count();
            $rataUmur = $query->clone()->avg('umur_lansia');
            
            return [
                'lakiLaki' => $lakiLaki,
                'perempuan' => $perempuan,
                'rataUmur' => $rataUmur ? round($rataUmur, 1) : 0
            ];
        });

        return view('admin.datalansia.index', array_merge(
            compact('datalansia', 'keyword', 'jenisKelamin', 'sort'),
            $stats
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Ambil semua user untuk dropdown (jika perlu)
        $users = User::all();
        return view('admin.datalansia.tambah', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'nama_lansia' => 'required|string|max:255',
            'umur_lansia' => 'required|integer|min:45|max:120',
            'tempat_lahir_lansia' => 'nullable|string|max:100',
            'tanggal_lahir_lansia' => 'nullable|date|before_or_equal:today',
            'jenis_kelamin_lansia' => 'required|in:Laki-laki,Perempuan',
            'gol_darah_lansia' => 'nullable|in:A,B,AB,O',
            'riwayat_penyakit_lansia' => 'nullable|string|max:1000',
            'alergi_lansia' => 'nullable|string|max:500',
            'obat_rutin_lansia' => 'nullable|string|max:500',
            'nama_anak' => 'required|string|max:255',
            'no_hp_anak' => 'required|string|max:15|regex:/^[0-9+\-\s]+$/',
            'email_anak' => 'nullable|email|max:255',
            'alamat_lengkap' => 'required|string|max:1000',
        ], [
            'nama_lansia.required' => 'Nama lansia wajib diisi',
            'umur_lansia.required' => 'Umur wajib diisi',
            'umur_lansia.min' => 'Umur minimal untuk lansia adalah 45 tahun',
            'umur_lansia.max' => 'Umur maksimal adalah 120 tahun',
            'jenis_kelamin_lansia.required' => 'Jenis kelamin wajib dipilih',
            'no_hp_anak.required' => 'Nomor HP anak wajib diisi untuk kontak darurat',
            'no_hp_anak.regex' => 'Format nomor HP tidak valid',
            'nama_anak.required' => 'Nama anak wajib diisi untuk kontak darurat',
            'alamat_lengkap.required' => 'Alamat lengkap wajib diisi',
        ]);

        // Tambahkan user_id dari user yang login
        $validated['user_id'] = auth()->id();
        
        // Otomatis hitung umur jika tanggal lahir diisi
        if ($request->tanggal_lahir_lansia) {
            $birthDate = Carbon::parse($request->tanggal_lahir_lansia);
            $validated['umur_lansia'] = $birthDate->age;
        }

        $datalansia = Datalansia::create($validated);
        
        // Clear cache statistik
        Cache::forget('stats_datalansia_all');
        
        return redirect()->route('admin.datalansia.index')
            ->with('success', 'Data lansia berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $datalansia = Datalansia::findOrFail($id);
        return view('admin.datalansia.show', compact('datalansia'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $datalansia = Datalansia::findOrFail($id);
        $users = User::all();
        return view('admin.datalansia.edit', compact('datalansia', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $datalansia = Datalansia::findOrFail($id);
        
        // Validasi input
        $validated = $request->validate([
            'nama_lansia' => 'required|string|max:255',
            'umur_lansia' => 'required|integer|min:45|max:120',
            'tempat_lahir_lansia' => 'nullable|string|max:100',
            'tanggal_lahir_lansia' => 'nullable|date|before_or_equal:today',
            'jenis_kelamin_lansia' => 'required|in:Laki-laki,Perempuan',
            'gol_darah_lansia' => 'nullable|in:A,B,AB,O',
            'riwayat_penyakit_lansia' => 'nullable|string|max:1000',
            'alergi_lansia' => 'nullable|string|max:500',
            'obat_rutin_lansia' => 'nullable|string|max:500',
            'nama_anak' => 'required|string|max:255',
            'no_hp_anak' => 'required|string|max:15|regex:/^[0-9+\-\s]+$/',
            'email_anak' => 'nullable|email|max:255',
            'alamat_lengkap' => 'required|string|max:1000',
        ], [
            'nama_lansia.required' => 'Nama lansia wajib diisi',
            'umur_lansia.required' => 'Umur wajib diisi',
            'umur_lansia.min' => 'Umur minimal untuk lansia adalah 45 tahun',
            'umur_lansia.max' => 'Umur maksimal adalah 120 tahun',
            'jenis_kelamin_lansia.required' => 'Jenis kelamin wajib dipilih',
            'no_hp_anak.required' => 'Nomor HP anak wajib diisi untuk kontak darurat',
            'no_hp_anak.regex' => 'Format nomor HP tidak valid',
            'nama_anak.required' => 'Nama anak wajib diisi untuk kontak darurat',
            'alamat_lengkap.required' => 'Alamat lengkap wajib diisi',
        ]);

        // Update umur otomatis jika tanggal lahir diubah
        if ($request->tanggal_lahir_lansia && 
            $request->tanggal_lahir_lansia != $datalansia->tanggal_lahir_lansia) {
            $birthDate = Carbon::parse($request->tanggal_lahir_lansia);
            $validated['umur_lansia'] = $birthDate->age;
        }

        $datalansia->update($validated);
        
        // Clear cache statistik
        Cache::forget('stats_datalansia_all');
        
        return redirect()->route('admin.datalansia.index')
            ->with('success', 'Data lansia berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $datalansia = Datalansia::findOrFail($id);
            $namaLansia = $datalansia->nama_lansia;
            
            // Cek jika data memiliki relasi lain sebelum menghapus
            // Contoh: if($datalansia->checkups()->count() > 0) {
            //     return back()->with('error', 'Tidak dapat menghapus karena ada data pemeriksaan terkait');
            // }
            
            $datalansia->delete();
            
            // Clear cache statistik
            Cache::forget('stats_datalansia_all');
            
            return redirect()->route('admin.datalansia.index')
                ->with('success', "Data lansia '{$namaLansia}' berhasil dihapus!");
                
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.datalansia.index')
                ->with('error', 'Data lansia tidak ditemukan!');
                
        } catch (\Exception $e) {
            return redirect()->route('admin.datalansia.index')
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    // Di DatalansiaController.php, perbaiki method export:

/**
 * Export data to Excel/PDF
 */
public function export(Request $request)
{
    try {
        Log::info('Export request received:', $request->all());
        
        $format = $request->get('format', 'excel');
        $columns = $request->get('columns') ? explode(',', $request->get('columns')) : null;
        
        // Generate filename
        $filename = 'data-lansia-' . date('Y-m-d-H-i-s');
        
        // Kirim parameter filter ke export class
        $filters = [
            'search' => $request->get('search'),
            'jenis_kelamin' => $request->get('jenis_kelamin'),
            'sort' => $request->get('sort'),
            'columns' => $columns
        ];
        
        if ($format === 'pdf') {
            // Untuk PDF, kita akan membuat view sederhana
            $filename .= '.pdf';
            return $this->exportPDF($filters, $filename);
        } else {
            // Untuk Excel
            $filename .= '.xlsx';
            return Excel::download(new DatalansiaExport($filters), $filename);
        }
        
    } catch (\Exception $e) {
        Log::error('Export error: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        
        return redirect()->back()
            ->with('error', 'Gagal mengexport data: ' . $e->getMessage());
    }
}

/**
 * Alternatif: Method export dengan GET parameter yang lebih sederhana
 */
public function export2(Request $request)
{
    try {
        $format = $request->query('format', 'excel');
        $columns = $request->query('columns');
        
        if ($columns) {
            $columns = explode(',', $columns);
        }
        
        // Ambil semua filter dari query string
        $filters = $request->only(['search', 'jenis_kelamin', 'sort']);
        $filters['columns'] = $columns;
        
        $filename = 'data-lansia-' . now()->format('Y-m-d-H-i-s');
        
        if ($format === 'pdf') {
            $filename .= '.pdf';
            return $this->exportPDF($filters, $filename);
        } else {
            $filename .= '.xlsx';
            return Excel::download(new DatalansiaExport($filters), $filename);
        }
        
    } catch (\Exception $e) {
        Log::error('Export error: ' . $e->getMessage());
        return back()->with('error', 'Gagal export: ' . $e->getMessage());
    }
}

    /**
     * Export to PDF (sederhana)
     */
    private function exportPDF($filters, $filename)
    {
        try {
            // Query data dengan filter
            $query = Datalansia::query();
            
            if (!empty($filters['search'])) {
                $query->where(function ($q) use ($filters) {
                    $q->where('nama_lansia', 'like', "%{$filters['search']}%")
                      ->orWhere('nama_anak', 'like', "%{$filters['search']}%")
                      ->orWhere('no_hp_anak', 'like', "%{$filters['search']}%")
                      ->orWhere('email_anak', 'like', "%{$filters['search']}%");
                });
            }
            
            if (!empty($filters['jenis_kelamin'])) {
                $query->where('jenis_kelamin_lansia', $filters['jenis_kelamin']);
            }
            
            // Apply sorting
            switch ($filters['sort'] ?? '') {
                case 'nama_asc':
                    $query->orderBy('nama_lansia', 'asc');
                    break;
                case 'nama_desc':
                    $query->orderBy('nama_lansia', 'desc');
                    break;
                case 'umur_asc':
                    $query->orderBy('umur_lansia', 'asc');
                    break;
                case 'umur_desc':
                    $query->orderBy('umur_lansia', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
            
            $datalansia = $query->get();
            
            // Kolom yang akan ditampilkan
            $columns = !empty($filters['columns']) ? $filters['columns'] : [
                'Nama Lansia', 'Umur', 'Jenis Kelamin', 'Tanggal Lahir', 
                'Nama Anak', 'No. HP Anak', 'Alamat'
            ];
            
            // Data untuk view
            $data = [
                'datalansia' => $datalansia,
                'columns' => $columns,
                'filters' => $filters,
                'total' => $datalansia->count(),
                'export_date' => now()->format('d/m/Y H:i:s'),
                'title' => 'Data Lansia'
            ];
            
            // Cek apakah DomPDF tersedia
            if (class_exists('Barryvdh\DomPDF\PDF')) {
                $pdf = \PDF::loadView('admin.datalansia.export-pdf', $data);
                return $pdf->download($filename);
            } else {
                // Fallback: Download sebagai CSV jika DomPDF tidak tersedia
                return $this->exportCSV($datalansia, $columns, str_replace('.pdf', '.csv', $filename));
            }
            
        } catch (\Exception $e) {
            Log::error('PDF Export error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Export to CSV (fallback)
     */
    private function exportCSV($datalansia, $columns, $filename)
    {
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=" . $filename,
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];
        
        $callback = function() use ($datalansia, $columns) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fwrite($file, "\xEF\xBB\xBF");
            
            // Write headers
            fputcsv($file, $columns);
            
            // Write data
            foreach ($datalansia as $item) {
                $row = [];
                
                foreach ($columns as $column) {
                    switch ($column) {
                        case 'Nama Lansia':
                            $row[] = $item->nama_lansia;
                            break;
                        case 'Umur':
                            $row[] = $item->umur_lansia . ' Tahun';
                            break;
                        case 'Jenis Kelamin':
                            $row[] = $item->jenis_kelamin_lansia;
                            break;
                        case 'Tanggal Lahir':
                            $row[] = $item->tanggal_lahir_lansia 
                                ? Carbon::parse($item->tanggal_lahir_lansia)->format('d/m/Y')
                                : '-';
                            break;
                        case 'Nama Anak':
                            $row[] = $item->nama_anak ?? '-';
                            break;
                        case 'No. HP Anak':
                            $row[] = $item->no_hp_anak ?? '-';
                            break;
                        case 'Email Anak':
                            $row[] = $item->email_anak ?? '-';
                            break;
                        case 'Alamat':
                            $row[] = $item->alamat_lengkap ?? '-';
                            break;
                        case 'Riwayat Penyakit':
                            $row[] = $item->riwayat_penyakit_lansia ?? '-';
                            break;
                        case 'Alergi':
                            $row[] = $item->alergi_lansia ?? '-';
                            break;
                        case 'Obat Rutin':
                            $row[] = $item->obat_rutin_lansia ?? '-';
                            break;
                        default:
                            $row[] = '';
                            break;
                    }
                }
                
                fputcsv($file, $row);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Simple export tanpa package (backup)
     */
    public function exportSimple(Request $request)
    {
        // Query data
        $query = Datalansia::query();
        
        if ($request->search) {
            $query->where('nama_lansia', 'like', "%{$request->search}%");
        }
        
        $data = $query->get();
        
        $filename = 'data-lansia-' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Nama', 'Umur', 'Jenis Kelamin', 'Nama Anak', 'No HP Anak']);
            
            foreach ($data as $item) {
                fputcsv($file, [
                    $item->nama_lansia,
                    $item->umur_lansia,
                    $item->jenis_kelamin_lansia,
                    $item->nama_anak,
                    $item->no_hp_anak
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Generate statistics (optional)
     */
    public function statistics()
    {
        try {
            $total = Datalansia::count();
            $lakiLaki = Datalansia::where('jenis_kelamin_lansia', 'Laki-laki')->count();
            $perempuan = Datalansia::where('jenis_kelamin_lansia', 'Perempuan')->count();
            $rataUmur = Datalansia::avg('umur_lansia');
            
            // Distribusi umur
            $umurDistribusi = [
                '45-59' => Datalansia::whereBetween('umur_lansia', [45, 59])->count(),
                '60-74' => Datalansia::whereBetween('umur_lansia', [60, 74])->count(),
                '75-89' => Datalansia::whereBetween('umur_lansia', [75, 89])->count(),
                '90+' => Datalansia::where('umur_lansia', '>=', 90)->count(),
            ];
            
            return response()->json([
                'success' => true,
                'data' => [
                    'total' => $total,
                    'laki_laki' => $lakiLaki,
                    'perempuan' => $perempuan,
                    'rata_umur' => round($rataUmur, 1),
                    'umur_distribusi' => $umurDistribusi,
                    'persentase_laki_laki' => $total > 0 ? round(($lakiLaki / $total) * 100, 1) : 0,
                    'persentase_perempuan' => $total > 0 ? round(($perempuan / $total) * 100, 1) : 0,
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik: ' . $e->getMessage()
            ], 500);
        }
    }
}