<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\IuranBulanan;
use App\Models\TemplateIuran;
use App\Models\Datalansia;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class IuranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = IuranBulanan::query()->with(['user', 'datalansia']);
            
            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            if ($request->filled('periode')) {
                $query->where('periode', $request->periode);
            }
            
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nama_iuran', 'like', '%' . $search . '%')
                      ->orWhere('kode_iuran', 'like', '%' . $search . '%')
                      ->orWhereHas('datalansia', function($q) use ($search) {
                          $q->where('nama_lansia', 'like', '%' . $search . '%');
                      })
                      ->orWhereHas('user', function($q) use ($search) {
                          $q->where('name', 'like', '%' . $search . '%');
                      });
                });
            }
            
            // Sort
            $sort = $request->get('sort', 'latest');
            switch ($sort) {
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'deadline':
                    $query->orderBy('tanggal_jatuh_tempo', 'asc');
                    break;
                case 'amount':
                    $query->orderBy('jumlah', 'desc');
                    break;
                default:
                    $query->latest();
            }
            
            $iurans = $query->paginate(20);
            
            // Calculate statistics
            $stats = [
                'total' => IuranBulanan::count(),
                'lunas' => IuranBulanan::where('status', 'lunas')->count(),
                'pending' => IuranBulanan::where('status', 'pending')->count(),
                'terlambat' => IuranBulanan::where('status', 'terlambat')->count(),
                'total_nominal' => IuranBulanan::where('status', 'lunas')->sum('jumlah'),
            ];
            
            // Get templates for generate modal
            $templates = TemplateIuran::where('is_active', true)->get();
            
            return view('admin.iuran.index', compact('iurans', 'stats', 'templates'));
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lansias = Datalansia::with('user')->get();
        $users = User::where('role', 'keluarga')->get();
        $templates = TemplateIuran::where('is_active', true)->get();
        
        return view('admin.iuran.create', compact('lansias', 'users', 'templates'));
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama_iuran' => 'required|string|max:255',
                'deskripsi' => 'nullable|string',
'jumlah' => 'required|numeric|min:1000',                'periode' => 'required|date_format:Y-m',
                'tanggal_jatuh_tempo' => 'required|date',
                'user_id' => 'nullable|exists:users,id',
                'datalansia_id' => 'nullable|exists:datalansia,id',
                'is_otomatis' => 'boolean',
                'interval_bulan' => 'nullable|integer|min:1|max:12',
                'catatan_admin' => 'nullable|string'
            ]);
            
            // Generate kode iuran
            $validated['kode_iuran'] = 'IUR-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -8));
            $validated['status'] = 'pending';
            
            // Set default untuk iuran otomatis
            if (!isset($validated['is_otomatis'])) {
                $validated['is_otomatis'] = false;
            }
            
            if ($validated['is_otomatis'] && empty($validated['interval_bulan'])) {
                $validated['interval_bulan'] = 1;
            }
            
            IuranBulanan::create($validated);
            
            return redirect()->route('admin.iuran.index')
                ->with('success', 'Iuran berhasil dibuat.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Generate iuran from template.
     */
    public function generateFromTemplate(Request $request)
    {
        try {
            $request->validate([
                'template_id' => 'required|exists:template_iuran,id',
                'periode' => 'required|date_format:Y-m'
            ]);
            
            $template = TemplateIuran::findOrFail($request->template_id);
            list($year, $month) = explode('-', $request->periode);
            
            $generated = $template->generateIuranForMonth($year, $month);
            
            return redirect()->route('admin.iuran.index')
                ->with('success', count($generated) . ' iuran berhasil digenerate.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Bulk generate iuran for multiple months.
     */
    public function bulkGenerate(Request $request)
    {
        try {
            $request->validate([
                'bulan_mulai' => 'required|date_format:Y-m',
                'bulan_selesai' => 'required|date_format:Y-m|after_or_equal:bulan_mulai',
                'template_id' => 'required|exists:template_iuran,id'
            ]);
            
            $template = TemplateIuran::findOrFail($request->template_id);
            $start = Carbon::parse($request->bulan_mulai . '-01');
            $end = Carbon::parse($request->bulan_selesai . '-01');
            
            $totalGenerated = 0;
            $current = $start->copy();
            
            while ($current->lte($end)) {
                $generated = $template->generateIuranForMonth(
                    $current->year,
                    $current->month
                );
                $totalGenerated += count($generated);
                $current->addMonth();
            }
            
            return redirect()->route('admin.iuran.index')
                ->with('success', $totalGenerated . ' iuran berhasil digenerate untuk periode ' . 
                       $request->bulan_mulai . ' sampai ' . $request->bulan_selesai);
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Mark iuran as paid.
     */
    public function markAsPaid(Request $request, $id)
    {
        try {
            $iuran = IuranBulanan::findOrFail($id);
            
            $iuran->update([
                'status' => 'lunas',
                'tanggal_bayar' => Carbon::now(),
                'metode_pembayaran' => 'manual_admin',
                'catatan_admin' => $iuran->catatan_admin . "\n[Dibayar via Admin: " . Carbon::now()->format('d/m/Y H:i') . "]"
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Iuran telah ditandai sebagai lunas'
                ]);
            }
            
            return back()->with('success', 'Iuran telah ditandai sebagai lunas.');
                
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Update iuran status.
     */
    public function updateStatus(Request $request, IuranBulanan $iuran)
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,lunas,terlambat,dibatalkan'
            ]);
            
            $oldStatus = $iuran->status;
            $newStatus = $request->status;
            
            $updateData = ['status' => $newStatus];
            
            // Jika status berubah menjadi lunas, set tanggal bayar
            if ($oldStatus != 'lunas' && $newStatus == 'lunas') {
                $updateData['tanggal_bayar'] = Carbon::now();
                if (!$iuran->metode_pembayaran) {
                    $updateData['metode_pembayaran'] = 'manual_admin';
                }
            }
            
            // Jika status berubah dari lunas ke lainnya, reset tanggal bayar
            if ($oldStatus == 'lunas' && $newStatus != 'lunas') {
                $updateData['tanggal_bayar'] = null;
            }
            
            $iuran->update($updateData);
            
            return back()->with('success', 'Status iuran berhasil diperbarui.');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(IuranBulanan $iuran)
    {
        try {
            $iuranName = $iuran->nama_iuran;
            $iuranCode = $iuran->kode_iuran;
            
            $iuran->delete();
            
            return back()->with('success', "Iuran {$iuranName} ({$iuranCode}) berhasil dihapus.");
                
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Check and update late iuran.
     */
    public function checkLateIuran()
    {
        try {
            $lateCount = IuranBulanan::where('status', 'pending')
                ->whereDate('tanggal_jatuh_tempo', '<', Carbon::now())
                ->update(['status' => 'terlambat']);
                
            return response()->json([
                'success' => true,
                'updated' => $lateCount,
                'message' => $lateCount . ' iuran diperbarui menjadi terlambat'
            ]);
                
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Export iuran data.
     */
    public function export(Request $request)
    {
        try {
            $query = IuranBulanan::query()->with(['user', 'datalansia']);
            
            // Apply filters same as index
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            if ($request->filled('periode')) {
                $query->where('periode', $request->periode);
            }
            
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nama_iuran', 'like', '%' . $search . '%')
                      ->orWhere('kode_iuran', 'like', '%' . $search . '%');
                });
            }
            
            $iurans = $query->get();
            
            // Generate CSV/Excel file
            $fileName = 'iuran_' . date('Ymd_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ];
            
            $callback = function() use ($iurans) {
                $file = fopen('php://output', 'w');
                
                // Header
                fputcsv($file, [
                    'Kode Iuran',
                    'Nama Iuran',
                    'Jumlah',
                    'Periode',
                    'Tanggal Jatuh Tempo',
                    'Nama Lansia',
                    'Nama Keluarga',
                    'Status',
                    'Tanggal Bayar',
                    'Metode Pembayaran'
                ]);
                
                // Data
                foreach ($iurans as $iuran) {
                    fputcsv($file, [
                        $iuran->kode_iuran,
                        $iuran->nama_iuran,
                        $iuran->jumlah,
                        $iuran->periode,
                        $iuran->tanggal_jatuh_tempo->format('d/m/Y'),
                        $iuran->datalansia ? $iuran->datalansia->nama_lansia : '-',
                        $iuran->user ? $iuran->user->name : '-',
                        $iuran->status,
                        $iuran->tanggal_bayar ? $iuran->tanggal_bayar->format('d/m/Y') : '-',
                        $iuran->metode_pembayaran ?? '-'
                    ]);
                }
                
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
                
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat export: ' . $e->getMessage());
        }
    }
    
    /**
     * Get iuran detail for modal.
     */
    public function detail(IuranBulanan $iuran)
    {
        try {
            $iuran->load(['user', 'datalansia']);
            
            return response()->json([
                'success' => true,
                'html' => view('admin.iuran.detail', compact('iuran'))->render()
            ]);
                
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Show statistics for dashboard.
     */
    public function statistics()
    {
        try {
            $today = Carbon::today();
            $monthStart = Carbon::now()->startOfMonth();
            $monthEnd = Carbon::now()->endOfMonth();
            
            $stats = [
                'total_iuran' => IuranBulanan::count(),
                'iuran_lunas_bulan_ini' => IuranBulanan::where('status', 'lunas')
                    ->whereBetween('tanggal_bayar', [$monthStart, $monthEnd])
                    ->sum('jumlah'),
                'iuran_terlambat' => IuranBulanan::where('status', 'terlambat')->count(),
                'iuran_akan_jatuh_tempo' => IuranBulanan::where('status', 'pending')
                    ->whereBetween('tanggal_jatuh_tempo', [$today, $today->copy()->addDays(7)])
                    ->count(),
                'top_lansia' => IuranBulanan::where('status', 'lunas')
                    ->whereBetween('tanggal_bayar', [$monthStart, $monthEnd])
                    ->groupBy('datalansia_id')
                    ->selectRaw('datalansia_id, SUM(jumlah) as total')
                    ->orderBy('total', 'desc')
                    ->with('datalansia')
                    ->take(5)
                    ->get()
            ];
            
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
                
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}