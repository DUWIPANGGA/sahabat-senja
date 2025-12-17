<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
    }
    
    /**
     * Menampilkan laporan pemasukan
     */
    /**
 * Get pemasukan data for edit (API)
 */
public function getPemasukanForEdit($id)
{
    try {
        $pemasukan = Pemasukan::with('user')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'pemasukan' => [
                'id' => $pemasukan->id,
                'tanggal' => $pemasukan->tanggal->format('Y-m-d'),
                'sumber' => $pemasukan->sumber,
                'jumlah' => $pemasukan->jumlah,
                'keterangan' => $pemasukan->keterangan,
                'user' => $pemasukan->user ? $pemasukan->user->name : null
            ]
        ]);
    } catch (\Exception $e) {
        \Log::error('Error getting pemasukan for edit: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Data tidak ditemukan'
        ], 404);
    }
}
    public function pemasukan(Request $request)
    {
        $query = Pemasukan::query();
        
        // Filter berdasarkan tanggal
        if ($request->filled('dari_tanggal')) {
            $query->whereDate('tanggal', '>=', $request->dari_tanggal);
        }
        
        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('tanggal', '<=', $request->sampai_tanggal);
        }
        
        // Filter berdasarkan sumber
        if ($request->filled('sumber')) {
            $query->where('sumber', 'like', '%' . $request->sumber . '%');
        }
        
        // Order by tanggal terbaru
        $query->orderBy('tanggal', 'desc');
        
        $pemasukan = $query->paginate(20);
        
        // Total pemasukan
        $totalPemasukan = $query->sum('jumlah');
        
        // Data untuk chart
        $chartData = $this->getChartDataPemasukan($request);
        
        return view('admin.laporan.laporan_pemasukan', compact('pemasukan', 'totalPemasukan', 'chartData'));
    }

    /**
     * Menampilkan laporan pengeluaran
     */
    public function pengeluaran(Request $request)
    {
        $query = Pengeluaran::query();
        
        // Filter berdasarkan tanggal
        if ($request->filled('dari_tanggal')) {
            $query->whereDate('tanggal', '>=', $request->dari_tanggal);
        }
        
        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('tanggal', '<=', $request->sampai_tanggal);
        }
        
        // Filter berdasarkan keterangan
        if ($request->filled('keterangan')) {
            $query->where('keterangan', 'like', '%' . $request->keterangan . '%');
        }
        
        // Order by tanggal terbaru
        $query->orderBy('tanggal', 'desc');
        
        $pengeluaran = $query->paginate(20);
        
        // Total pengeluaran
        $totalPengeluaran = $query->sum('jumlah');
        
        // Data untuk chart
        $chartData = $this->getChartDataPengeluaran($request);
        
        return view('admin.laporan.laporan_pengeluaran', compact('pengeluaran', 'totalPengeluaran', 'chartData'));
    }

    /**
     * Menampilkan dashboard laporan (total pemasukan dan pengeluaran)
     */
    public function dashboard(Request $request)
    {
        $periode = $request->periode ?? 'bulan_ini';
        
        // Query untuk pemasukan
        $queryPemasukan = Pemasukan::query();
        $queryPengeluaran = Pengeluaran::query();
        
        // Filter berdasarkan periode
        switch ($periode) {
            case 'hari_ini':
                $queryPemasukan->whereDate('tanggal', Carbon::today());
                $queryPengeluaran->whereDate('tanggal', Carbon::today());
                break;
            case 'minggu_ini':
                $queryPemasukan->whereBetween('tanggal', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                $queryPengeluaran->whereBetween('tanggal', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'bulan_ini':
                $queryPemasukan->whereMonth('tanggal', Carbon::now()->month)
                              ->whereYear('tanggal', Carbon::now()->year);
                $queryPengeluaran->whereMonth('tanggal', Carbon::now()->month)
                                ->whereYear('tanggal', Carbon::now()->year);
                break;
            case 'tahun_ini':
                $queryPemasukan->whereYear('tanggal', Carbon::now()->year);
                $queryPengeluaran->whereYear('tanggal', Carbon::now()->year);
                break;
        }
        
        $totalPemasukan = $queryPemasukan->sum('jumlah');
        $totalPengeluaran = $queryPengeluaran->sum('jumlah');
        $saldo = $totalPemasukan - $totalPengeluaran;
        
        // Data untuk chart
        $chartPemasukan = $this->getChartDataDashboard('pemasukan', $periode);
        $chartPengeluaran = $this->getChartDataDashboard('pengeluaran', $periode);
        
        return view('admin.laporan.dashboard', compact(
            'totalPemasukan', 
            'totalPengeluaran', 
            'saldo',
            'chartPemasukan',
            'chartPengeluaran',
            'periode'
        ));
    }

    /**
     * Menyimpan data pemasukan baru
     */
    public function storePemasukan(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'sumber' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string'
        ]);

        Pemasukan::create([
            'tanggal' => $request->tanggal,
            'sumber' => $request->sumber,
            'jumlah' => $request->jumlah,
            'keterangan' => $request->keterangan,
            'user_id' => auth()->id()
        ]);

        return redirect()->route('laporan.pemasukan')
            ->with('success', 'Data pemasukan berhasil ditambahkan.');
    }

    /**
     * Menyimpan data pengeluaran baru
     */
    public function getPengeluaranForEdit($id)
    {
        try {
            $pengeluaran = Pengeluaran::with('user')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'pengeluaran' => [
                    'id' => $pengeluaran->id,
                    'tanggal' => $pengeluaran->tanggal->format('Y-m-d'),
                    'keterangan' => $pengeluaran->keterangan,
                    'jumlah' => $pengeluaran->jumlah,
                    'bukti' => $pengeluaran->bukti,
                    'user' => $pengeluaran->user ? $pengeluaran->user->name : null
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }
    
    public function storePengeluaran(Request $request)
{
    $request->validate([
        'tanggal' => 'required|date',
        'keterangan' => 'required|string|max:255',
        'jumlah' => 'required|numeric|min:0',
        'bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048' // Perbaiki validasi
    ]);

    $data = [
        'tanggal' => $request->tanggal,
        'keterangan' => $request->keterangan,
        'jumlah' => $request->jumlah,
        'user_id' => auth()->id()
    ];

    // Handle file upload
    if ($request->hasFile('bukti')) {
        $path = $request->file('bukti')->store('bukti-pengeluaran', 'public');
        $data['bukti'] = $path;
    }

    Pengeluaran::create($data);

    return redirect()->route('laporan.pengeluaran')
        ->with('success', 'Data pengeluaran berhasil ditambahkan.');
}

    /**
     * Menampilkan form edit pemasukan
     */
    public function editPemasukan($id)
    {
        $pemasukan = Pemasukan::findOrFail($id);
        return view('admin.laporan.edit_pemasukan', compact('pemasukan'));
    }

    /**
     * Update data pemasukan
     */
     public function updatePemasukan(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'sumber' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string'
        ]);

        try {
            $pemasukan = Pemasukan::findOrFail($id);
            $pemasukan->update([
                'tanggal' => $request->tanggal,
                'sumber' => $request->sumber,
                'jumlah' => $request->jumlah,
                'keterangan' => $request->keterangan
            ]);

            return redirect()->route('laporan.pemasukan')
                ->with('success', 'Data pemasukan berhasil diperbarui.');
                
        } catch (\Exception $e) {
            \Log::error('Error updating pemasukan: ' . $e->getMessage());
            return redirect()->route('laporan.pemasukan')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    /**
     * Menampilkan form edit pengeluaran
     */
    public function editPengeluaran($id)
    {
        $pengeluaran = Pengeluaran::findOrFail($id);
        return view('admin.laporan.edit_pengeluaran', compact('pengeluaran'));
    }

    /**
     * Update data pengeluaran
     */
    public function updatePengeluaran(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'keterangan' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'bukti' => 'nullable|string'
        ]);

        $pengeluaran = Pengeluaran::findOrFail($id);
        $pengeluaran->update([
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'jumlah' => $request->jumlah,
            'bukti' => $request->bukti
        ]);

        return redirect()->route('laporan.pengeluaran')
            ->with('success', 'Data pengeluaran berhasil diperbarui.');
    }

    /**
     * Menghapus data pemasukan
     */
    public function destroyPemasukan($id)
    {
        $pemasukan = Pemasukan::findOrFail($id);
        $pemasukan->delete();

        return redirect()->route('laporan.pemasukan')
            ->with('success', 'Data pemasukan berhasil dihapus.');
    }

    /**
     * Menghapus data pengeluaran
     */
    public function destroyPengeluaran($id)
    {
        $pengeluaran = Pengeluaran::findOrFail($id);
        $pengeluaran->delete();

        return redirect()->route('laporan.pengeluaran')
            ->with('success', 'Data pengeluaran berhasil dihapus.');
    }

    /**
     * Get chart data for pemasukan
     */
    private function getChartDataPemasukan(Request $request)
    {
        $query = Pemasukan::select(
            DB::raw('DATE_FORMAT(tanggal, "%Y-%m") as bulan_tahun'),
            DB::raw('SUM(jumlah) as total')
        )
        ->groupBy('bulan_tahun')
        ->orderBy('bulan_tahun', 'asc')
        ->limit(12);

        if ($request->filled('dari_tanggal')) {
            $query->whereDate('tanggal', '>=', $request->dari_tanggal);
        }

        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('tanggal', '<=', $request->sampai_tanggal);
        }

        return $query->get();
    }

    /**
     * Get chart data for pengeluaran
     */
    private function getChartDataPengeluaran(Request $request)
    {
        $query = Pengeluaran::select(
            DB::raw('DATE_FORMAT(tanggal, "%Y-%m") as bulan_tahun'),
            DB::raw('SUM(jumlah) as total')
        )
        ->groupBy('bulan_tahun')
        ->orderBy('bulan_tahun', 'asc')
        ->limit(12);

        if ($request->filled('dari_tanggal')) {
            $query->whereDate('tanggal', '>=', $request->dari_tanggal);
        }

        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('tanggal', '<=', $request->sampai_tanggal);
        }

        return $query->get();
    }

    /**
     * Get chart data for dashboard
     */
    private function getChartDataDashboard($type, $periode)
    {
        $model = $type === 'pemasukan' ? new Pemasukan() : new Pengeluaran();
        $query = $model->select(
            DB::raw('DATE_FORMAT(tanggal, "%Y-%m") as bulan_tahun'),
            DB::raw('SUM(jumlah) as total')
        )
        ->groupBy('bulan_tahun')
        ->orderBy('bulan_tahun', 'desc')
        ->limit(6);

        // Filter berdasarkan periode
        $now = Carbon::now();
        switch ($periode) {
            case 'tahun_ini':
                $query->whereYear('tanggal', $now->year);
                break;
            case 'bulan_ini':
                $query->whereMonth('tanggal', $now->month)
                      ->whereYear('tanggal', $now->year);
                break;
            case 'minggu_ini':
                $query->whereBetween('tanggal', [$now->startOfWeek(), $now->endOfWeek()]);
                break;
        }

        return $query->get()->reverse();
    }

    /**
     * Export laporan pemasukan ke PDF
     */
    public function exportPemasukanPdf(Request $request)
    {
        // Implementasi export PDF
        $data = $this->getExportData($request, 'pemasukan');
        // Gunakan library seperti DomPDF atau Barryvdh/Laravel-DomPDF
    }

    /**
     * Export laporan pengeluaran ke PDF
     */
    public function exportPengeluaranPdf(Request $request)
    {
        // Implementasi export PDF
        $data = $this->getExportData($request, 'pengeluaran');
        // Gunakan library seperti DomPDF atau Barryvdh/Laravel-DomPDF
    }

    /**
     * Get data for export
     */
    private function getExportData(Request $request, $type)
    {
        $model = $type === 'pemasukan' ? new Pemasukan() : new Pengeluaran();
        $query = $model->query();
        
        if ($request->filled('dari_tanggal')) {
            $query->whereDate('tanggal', '>=', $request->dari_tanggal);
        }
        
        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('tanggal', '<=', $request->sampai_tanggal);
        }
        
        return $query->orderBy('tanggal', 'desc')->get();
    }
}