<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanController extends Controller
{
    /**
     * Menampilkan laporan pemasukan
     */
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
        
        return view('admin.laporan.pemasukan', compact('pemasukan', 'totalPemasukan', 'chartData'));
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
        
        return view('admin.laporan.pengeluaran', compact('pengeluaran', 'totalPengeluaran', 'chartData'));
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
    public function storePengeluaran(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'keterangan' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'bukti' => 'nullable|string'
        ]);

        Pengeluaran::create([
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'jumlah' => $request->jumlah,
            'bukti' => $request->bukti,
            'user_id' => auth()->id()
        ]);

        return redirect()->route('laporan.pengeluaran')
            ->with('success', 'Data pengeluaran berhasil ditambahkan.');
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
            DB::raw('MONTH(tanggal) as bulan'),
            DB::raw('YEAR(tanggal) as tahun'),
            DB::raw('SUM(jumlah) as total')
        )
        ->groupBy(DB::raw('YEAR(tanggal), MONTH(tanggal)'))
        ->orderBy('tahun', 'asc')
        ->orderBy('bulan', 'asc')
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
            DB::raw('MONTH(tanggal) as bulan'),
            DB::raw('YEAR(tanggal) as tahun'),
            DB::raw('SUM(jumlah) as total')
        )
        ->groupBy(DB::raw('YEAR(tanggal), MONTH(tanggal)'))
        ->orderBy('tahun', 'asc')
        ->orderBy('bulan', 'asc')
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
     * Export laporan pemasukan ke PDF
     */
    public function exportPemasukanPdf(Request $request)
    {
        // Implementasi export PDF
    }

    /**
     * Export laporan pengeluaran ke PDF
     */
    public function exportPengeluaranPdf(Request $request)
    {
        // Implementasi export PDF
    }

    /**
     * Export laporan pemasukan ke Excel
     */
    public function exportPemasukanExcel(Request $request)
    {
        // Implementasi export Excel
    }

    /**
     * Export laporan pengeluaran ke Excel
     */
    public function exportPengeluaranExcel(Request $request)
    {
        // Implementasi export Excel
    }
}