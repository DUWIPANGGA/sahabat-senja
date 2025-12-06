<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kamar;
use App\Models\Datalansia;
use App\Models\JadwalObat;
use App\Models\DataPerawat;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        // Get filter parameters for financial chart
        $tahun = $request->get('tahun', date('Y'));
        $rentang = $request->get('rentang', 'bulan');
        
        // Total counts
        $total_lansia = Datalansia::count();
        $total_perawat = DataPerawat::count();
        
        // Data per bulan untuk grafik lansia dan perawat
        $perawat_monthly = $this->getPerawatMonthlyData();
        $lansia_monthly = $this->getLansiaMonthlyData();
        
        // Data keuangan real untuk grafik
        $chartData = $this->getFinancialChartData($tahun, $rentang);
        
        // Data untuk statistik tambahan
        // Karena tidak ada kolom status, kita anggap semua lansia aktif
        $lansia_aktif = $total_lansia;
        
        // Cek apakah perawat punya kolom status
        try {
            $perawat_aktif = DataPerawat::where('status', 'Aktif')->count();
        } catch (\Exception $e) {
            // Jika tidak ada kolom status, anggap semua perawat aktif
            $perawat_aktif = $total_perawat;
        }
        
        // Calculate financial stats
        $totalPemasukan = Pemasukan::whereYear('tanggal', $tahun)->sum('jumlah');
        $totalPengeluaran = Pengeluaran::whereYear('tanggal', $tahun)->sum('jumlah');
        $saldoBersih = $totalPemasukan - $totalPengeluaran;
        
        // Get recent financial transactions
        $transaksiTerbaru = $this->getRecentFinancialTransactions();
        
        // Get recent activities (combined)
        $recent_activities = $this->getRecentActivities();
        
        // Ratio perawat vs lansia
        $ratio = ($total_lansia > 0 && $total_perawat > 0) 
            ? round($total_lansia / $total_perawat, 1) 
            : 0;
        
        // Data untuk distribusi lansia (berdasarkan usia)
        $distribusi_usia = $this->getDistribusiUsiaLansia();
        
        // Data untuk distribusi jenis kelamin
        $distribusi_gender = $this->getDistribusiGenderLansia();
        
        // Data untuk penyakit umum
        $penyakit_umum = $this->getPenyakitUmum();
        
        // Data bulan lalu untuk comparison
        $last_month_data = $this->getLastMonthData();
        
        // Stats array
        $stats = [
            'total_lansia' => $total_lansia,
            'total_perawat' => $total_perawat,
            'total_lansia_last_month' => $last_month_data['lansia'] ?? 0,
            'total_perawat_last_month' => $last_month_data['perawat'] ?? 0,
            'lansia_aktif' => $lansia_aktif,
            'perawat_aktif' => $perawat_aktif,
            'ratio' => $ratio,
            'distribusi_usia' => $distribusi_usia,
            'distribusi_gender' => $distribusi_gender,
            'penyakit_umum' => $penyakit_umum,
            'total_pemasukan' => $totalPemasukan,
            'total_pengeluaran' => $totalPengeluaran,
            'saldo_bersih' => $saldoBersih,
        ];
        
        return view('admin.dashboard', compact(
            'stats', 
            'perawat_monthly', 
            'lansia_monthly',
            'recent_activities',
            'chartData',
            'transaksiTerbaru',
            'tahun',
            'rentang'
        ));
    }
    
    private function getFinancialChartData($tahun, $rentang)
    {
        $labels = [];
        $pemasukanData = [];
        $pengeluaranData = [];
        
        if ($rentang === 'bulan') {
            // Data per bulan untuk tahun yang dipilih
            $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 
                          'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            
            for ($month = 1; $month <= 12; $month++) {
                $labels[] = $monthNames[$month - 1];
                
                // Get pemasukan for this month
                $pemasukan = Pemasukan::whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', $month)
                    ->sum('jumlah');
                $pemasukanData[] = (float) $pemasukan;
                
                // Get pengeluaran for this month
                $pengeluaran = Pengeluaran::whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', $month)
                    ->sum('jumlah');
                $pengeluaranData[] = (float) $pengeluaran;
            }
        } elseif ($rentang === 'minggu') {
            // Data per minggu untuk 4 minggu terakhir
            for ($week = 3; $week >= 0; $week--) {
                $startDate = Carbon::now()->subWeeks($week)->startOfWeek();
                $endDate = Carbon::now()->subWeeks($week)->endOfWeek();
                
                $labels[] = 'Minggu ' . ($week + 1) . ' (' . $startDate->format('d/m') . ')';
                
                // Get pemasukan for this week
                $pemasukan = Pemasukan::whereBetween('tanggal', [$startDate, $endDate])
                    ->sum('jumlah');
                $pemasukanData[] = (float) $pemasukan;
                
                // Get pengeluaran for this week
                $pengeluaran = Pengeluaran::whereBetween('tanggal', [$startDate, $endDate])
                    ->sum('jumlah');
                $pengeluaranData[] = (float) $pengeluaran;
            }
        } else {
            // Data per hari untuk 30 hari terakhir
            for ($day = 29; $day >= 0; $day--) {
                $date = Carbon::now()->subDays($day);
                $labels[] = $date->format('d/m');
                
                // Get pemasukan for this day
                $pemasukan = Pemasukan::whereDate('tanggal', $date)
                    ->sum('jumlah');
                $pemasukanData[] = (float) $pemasukan;
                
                // Get pengeluaran for this day
                $pengeluaran = Pengeluaran::whereDate('tanggal', $date)
                    ->sum('jumlah');
                $pengeluaranData[] = (float) $pengeluaran;
            }
        }
        
        return [
            'labels' => $labels,
            'pemasukan' => $pemasukanData,
            'pengeluaran' => $pengeluaranData
        ];
    }
    
    private function getRecentFinancialTransactions($limit = 4)
    {
        // Get recent pemasukan
        $pemasukan = Pemasukan::select(
            'id',
            'tanggal',
            'sumber as description',
            'jumlah',
            DB::raw("'pemasukan' as type")
        )
        ->orderBy('tanggal', 'desc')
        ->limit($limit);
        
        // Get recent pengeluaran
        $pengeluaran = Pengeluaran::select(
            'id',
            'tanggal',
            'keterangan as description',
            'jumlah',
            DB::raw("'pengeluaran' as type")
        )
        ->orderBy('tanggal', 'desc')
        ->limit($limit);
        
        // Combine and sort
        $transactions = $pemasukan->unionAll($pengeluaran)
            ->orderBy('tanggal', 'desc')
            ->limit($limit)
            ->get();
        
        return $transactions;
    }
    
    private function getPerawatMonthlyData()
    {
        $currentYear = date('Y');
        $monthlyData = array_fill(0, 12, 0);
        
        // Ambil data perawat yang dibuat per bulan
        $data = DataPerawat::select(
                DB::raw('MONTH(created_at) as bulan'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('created_at', $currentYear)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->get();
        
        foreach ($data as $item) {
            $monthlyData[$item->bulan - 1] = (int) $item->total;
        }
        
        // Untuk bulan-bulan tanpa data, hitung akumulatif
        $runningTotal = 0;
        for ($i = 0; $i < 12; $i++) {
            if ($monthlyData[$i] > 0) {
                $runningTotal += $monthlyData[$i];
                $monthlyData[$i] = $runningTotal;
            } else if ($i > 0) {
                $monthlyData[$i] = $runningTotal;
            }
        }
        
        return $monthlyData;
    }
    
    private function getLansiaMonthlyData()
    {
        $currentYear = date('Y');
        $monthlyData = array_fill(0, 12, 0);
        
        // Ambil data lansia yang dibuat per bulan
        $data = Datalansia::select(
                DB::raw('MONTH(created_at) as bulan'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('created_at', $currentYear)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->get();
        
        foreach ($data as $item) {
            $monthlyData[$item->bulan - 1] = (int) $item->total;
        }
        
        // Untuk bulan-bulan tanpa data, hitung akumulatif
        $runningTotal = 0;
        for ($i = 0; $i < 12; $i++) {
            if ($monthlyData[$i] > 0) {
                $runningTotal += $monthlyData[$i];
                $monthlyData[$i] = $runningTotal;
            } else if ($i > 0) {
                $monthlyData[$i] = $runningTotal;
            }
        }
        
        return $monthlyData;
    }
    
    private function getLastMonthData()
    {
        $lastMonth = Carbon::now()->subMonth();
        
        $lansia_last_month = Datalansia::whereYear('created_at', $lastMonth->year)
            ->whereMonth('created_at', $lastMonth->month)
            ->count();
            
        $perawat_last_month = DataPerawat::whereYear('created_at', $lastMonth->year)
            ->whereMonth('created_at', $lastMonth->month)
            ->count();
        
        return [
            'lansia' => $lansia_last_month,
            'perawat' => $perawat_last_month
        ];
    }
    
    private function getDistribusiUsiaLansia()
    {
        $data = Datalansia::select('umur_lansia', 'tanggal_lahir_lansia')->get();
        
        $distribusi = [
            '60-69' => 0,
            '70-79' => 0,
            '80-89' => 0,
            '90+' => 0
        ];
        
        foreach ($data as $lansia) {
            // Gunakan umur_lansia jika ada, jika tidak hitung dari tanggal lahir
            if ($lansia->umur_lansia) {
                $usia = $lansia->umur_lansia;
            } elseif ($lansia->tanggal_lahir_lansia) {
                try {
                    $usia = Carbon::parse($lansia->tanggal_lahir_lansia)->age;
                } catch (\Exception $e) {
                    continue;
                }
            } else {
                continue;
            }
            
            if ($usia >= 60 && $usia <= 69) {
                $distribusi['60-69']++;
            } elseif ($usia >= 70 && $usia <= 79) {
                $distribusi['70-79']++;
            } elseif ($usia >= 80 && $usia <= 89) {
                $distribusi['80-89']++;
            } elseif ($usia >= 90) {
                $distribusi['90+']++;
            }
        }
        
        return $distribusi;
    }
    
    private function getDistribusiGenderLansia()
    {
        return Datalansia::select('jenis_kelamin_lansia', DB::raw('COUNT(*) as total'))
            ->groupBy('jenis_kelamin_lansia')
            ->pluck('total', 'jenis_kelamin_lansia')
            ->toArray();
    }
    
    private function getPenyakitUmum()
    {
        // Ambil data riwayat penyakit dan hitung frekuensi
        $data = Datalansia::select('riwayat_penyakit_lansia')->get();
        
        $penyakit_counts = [];
        foreach ($data as $lansia) {
            if ($lansia->riwayat_penyakit_lansia) {
                $penyakit = trim($lansia->riwayat_penyakit_lansia);
                if (isset($penyakit_counts[$penyakit])) {
                    $penyakit_counts[$penyakit]++;
                } else {
                    $penyakit_counts[$penyakit] = 1;
                }
            }
        }
        
        arsort($penyakit_counts); // Urutkan dari terbanyak
        return array_slice($penyakit_counts, 0, 5, true); // Ambil 5 teratas
    }
    
    private function getRecentActivities()
    {
        $recent_activities = [];
        
        // Get recent financial transactions (2 most recent)
        $financial_transactions = $this->getRecentFinancialTransactions(2);
        foreach ($financial_transactions as $transaction) {
            $recent_activities[] = [
                'title' => ($transaction->type == 'pemasukan' ? 'Pemasukan: ' : 'Pengeluaran: ') . 
                          ($transaction->description ?? 'Transaksi'),
                'time' => Carbon::parse($transaction->tanggal)->diffForHumans(),
                'icon' => $transaction->type == 'pemasukan' ? 'fa-money-bill-wave' : 'fa-hand-holding-usd'
            ];
        }
        
        // Data lansia terbaru (2 terbaru)
        $lansia_terbaru = Datalansia::latest()->take(2)->get();
        foreach ($lansia_terbaru as $lansia) {
            $recent_activities[] = [
                'title' => 'Lansia baru ditambahkan: ' . $lansia->nama_lansia,
                'time' => Carbon::parse($lansia->created_at)->diffForHumans(),
                'icon' => 'fa-user-plus'
            ];
        }
        
        // Data perawat terbaru (2 terbaru)
        $perawat_terbaru = DataPerawat::latest()->take(2)->get();
        foreach ($perawat_terbaru as $perawat) {
            $recent_activities[] = [
                'title' => 'Perawat baru bergabung: ' . $perawat->nama,
                'time' => Carbon::parse($perawat->created_at)->diffForHumans(),
                'icon' => 'fa-user-md'
            ];
        }
        
        // Sort by time (newest first)
        usort($recent_activities, function($a, $b) {
            return Carbon::parse($b['time'])->timestamp - Carbon::parse($a['time'])->timestamp;
        });
        
        return array_slice($recent_activities, 0, 6);
    }
    
    // Jika Anda ingin memisahkan aktivitas keuangan dari aktivitas umum
    private function getRecentActivitiesSeparated()
    {
        $financial_activities = [];
        $general_activities = [];
        
        // Recent financial transactions
        $financial_transactions = $this->getRecentFinancialTransactions(3);
        foreach ($financial_transactions as $transaction) {
            $financial_activities[] = [
                'title' => ($transaction->type == 'pemasukan' ? 'Pemasukan: ' : 'Pengeluaran: ') . 
                          ($transaction->description ?? 'Transaksi'),
                'time' => Carbon::parse($transaction->tanggal)->diffForHumans(),
                'icon' => $transaction->type == 'pemasukan' ? 'fa-money-bill-wave' : 'fa-hand-holding-usd'
            ];
        }
        
        // Recent general activities
        $lansia_terbaru = Datalansia::latest()->take(2)->get();
        foreach ($lansia_terbaru as $lansia) {
            $general_activities[] = [
                'title' => 'Lansia baru ditambahkan: ' . $lansia->nama_lansia,
                'time' => Carbon::parse($lansia->created_at)->diffForHumans(),
                'icon' => 'fa-user-plus'
            ];
        }
        
        $perawat_terbaru = DataPerawat::latest()->take(2)->get();
        foreach ($perawat_terbaru as $perawat) {
            $general_activities[] = [
                'title' => 'Perawat baru bergabung: ' . $perawat->nama,
                'time' => Carbon::parse($perawat->created_at)->diffForHumans(),
                'icon' => 'fa-user-md'
            ];
        }
        
        // Sort general activities by time
        usort($general_activities, function($a, $b) {
            return Carbon::parse($b['time'])->timestamp - Carbon::parse($a['time'])->timestamp;
        });
        
        return [
            'financial' => array_slice($financial_activities, 0, 3),
            'general' => array_slice($general_activities, 0, 3)
        ];
    }
}