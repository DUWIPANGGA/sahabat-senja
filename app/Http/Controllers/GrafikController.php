<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GrafikController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $bulan = $request->get('bulan', date('m'));
        $userId = Auth::id();
        
        // Data untuk chart utama (per bulan dalam tahun)
        $pemasukanPerBulan = $this->getDataPerBulan($tahun, 'pemasukan');
        $pengeluaranPerBulan = $this->getDataPerBulan($tahun, 'pengeluaran');
        
        // Data untuk donut chart (per kategori dalam bulan)
        $pemasukanBySumber = $this->getPemasukanBySumber($tahun, $bulan);
        $pengeluaranByKategori = $this->getPengeluaranByKategori($tahun, $bulan);
        
        // Recent transactions
        $recentPemasukan = Pemasukan::where('user_id', $userId)
            ->orderBy('tanggal', 'desc')
            ->limit(5)
            ->get();
            
        $recentPengeluaran = Pengeluaran::where('user_id', $userId)
            ->orderBy('tanggal', 'desc')
            ->limit(5)
            ->get();
        
        // Totals
        $totalPemasukan = array_sum($pemasukanPerBulan);
        $totalPengeluaran = array_sum($pengeluaranPerBulan);
        $saldo = $totalPemasukan - $totalPengeluaran;
        
        // Data bulan lalu untuk comparison
        $bulanLalu = Carbon::now()->subMonth();
        $totalPemasukanBulanLalu = $this->getTotalBulanLalu($bulanLalu->year, $bulanLalu->month, 'pemasukan');
        $totalPengeluaranBulanLalu = $this->getTotalBulanLalu($bulanLalu->year, $bulanLalu->month, 'pengeluaran');
        $saldoBulanLalu = $totalPemasukanBulanLalu - $totalPengeluaranBulanLalu;
        
        return view('admin.grafik.index', [
            'tahun' => $tahun,
            'bulan' => $bulan,
            'namaBulan' => $this->getNamaBulan($bulan),
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            'pemasukanData' => array_values($pemasukanPerBulan),
            'pengeluaranData' => array_values($pengeluaranPerBulan),
            'pemasukanLabels' => array_keys($pemasukanBySumber),
            'pemasukanValues' => array_values($pemasukanBySumber),
            'pengeluaranLabels' => array_keys($pengeluaranByKategori),
            'pengeluaranValues' => array_values($pengeluaranByKategori),
            'totalPemasukan' => $totalPemasukan,
            'totalPengeluaran' => $totalPengeluaran,
            'saldo' => $saldo,
            'totalPemasukanBulanLalu' => $totalPemasukanBulanLalu,
            'totalPengeluaranBulanLalu' => $totalPengeluaranBulanLalu,
            'saldoBulanLalu' => $saldoBulanLalu,
            'tahunList' => $this->getTahunList(),
            'recentPemasukan' => $recentPemasukan,
            'recentPengeluaran' => $recentPengeluaran,
        ]);
    }
    
    private function getDataPerBulan($tahun, $tipe)
    {
        $userId = Auth::id();
        $dataPerBulan = array_fill(0, 12, 0);
        
        if ($tipe === 'pemasukan') {
            $query = Pemasukan::select(
                DB::raw('MONTH(tanggal) as bulan'),
                DB::raw('SUM(jumlah) as total')
            )
            ->whereYear('tanggal', $tahun)
            ->where('user_id', $userId)
            ->groupBy(DB::raw('MONTH(tanggal)'))
            ->get();
        } else {
            $query = Pengeluaran::select(
                DB::raw('MONTH(tanggal) as bulan'),
                DB::raw('SUM(jumlah) as total')
            )
            ->whereYear('tanggal', $tahun)
            ->where('user_id', $userId)
            ->groupBy(DB::raw('MONTH(tanggal)'))
            ->get();
        }
        
        foreach ($query as $item) {
            $dataPerBulan[$item->bulan - 1] = (float) $item->total;
        }
        
        return $dataPerBulan;
    }
    
    private function getPemasukanBySumber($tahun, $bulan)
    {
        $userId = Auth::id();
        
        $data = Pemasukan::select(
                'sumber',
                DB::raw('SUM(jumlah) as total')
            )
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->where('user_id', $userId)
            ->groupBy('sumber')
            ->orderBy('total', 'desc')
            ->get()
            ->pluck('total', 'sumber')
            ->toArray();
        
        // Limit to top 6 sources
        arsort($data);
        $data = array_slice($data, 0, 6, true);
        
        return $data;
    }
    
    private function getPengeluaranByKategori($tahun, $bulan)
    {
        $userId = Auth::id();
        
        $data = Pengeluaran::select(
                'keterangan',
                DB::raw('SUM(jumlah) as total')
            )
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->where('user_id', $userId)
            ->groupBy('keterangan')
            ->orderBy('total', 'desc')
            ->get()
            ->pluck('total', 'keterangan')
            ->toArray();
        
        // Limit to top 6 categories
        arsort($data);
        $data = array_slice($data, 0, 6, true);
        
        return $data;
    }
    
    private function getTotalBulanLalu($tahun, $bulan, $tipe)
    {
        $userId = Auth::id();
        
        if ($tipe === 'pemasukan') {
            return Pemasukan::whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $bulan)
                ->where('user_id', $userId)
                ->sum('jumlah') ?? 0;
        } else {
            return Pengeluaran::whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $bulan)
                ->where('user_id', $userId)
                ->sum('jumlah') ?? 0;
        }
    }
    
    private function getTahunList()
    {
        $userId = Auth::id();
        
        $tahunPemasukan = Pemasukan::select(DB::raw('YEAR(tanggal) as tahun'))
            ->where('user_id', $userId)
            ->groupBy(DB::raw('YEAR(tanggal)'))
            ->pluck('tahun')
            ->toArray();
        
        $tahunPengeluaran = Pengeluaran::select(DB::raw('YEAR(tanggal) as tahun'))
            ->where('user_id', $userId)
            ->groupBy(DB::raw('YEAR(tanggal)'))
            ->pluck('tahun')
            ->toArray();
        
        $tahunList = array_unique(array_merge($tahunPemasukan, $tahunPengeluaran));
        rsort($tahunList);
        
        if (empty($tahunList)) {
            $tahunList = [date('Y')];
        }
        
        return $tahunList;
    }
    
    private function getNamaBulan($bulan)
    {
        $bulanList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        return $bulanList[$bulan] ?? 'Unknown';
    }
}