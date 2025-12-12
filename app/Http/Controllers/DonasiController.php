<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Donasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Exports\DonasiExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class DonasiController extends Controller
{
    public function index(Request $request)
    {
        $query = Donasi::with(['kampanye', 'user']);
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('metode_pembayaran')) {
            $query->where('metode_pembayaran', $request->metode_pembayaran);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_donatur', 'like', '%' . $search . '%')
                  ->orWhere('kode_donasi', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }
        
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        // Sort
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'amount_asc':
                $query->orderBy('jumlah', 'asc');
                break;
            case 'amount_desc':
                $query->orderBy('jumlah', 'desc');
                break;
            default:
                $query->latest();
        }
        
        $donasis = $query->paginate(20);
        
        // Calculate statistics
        $stats = [
            'total' => Donasi::count(),
            'success' => Donasi::where('status', 'success')->count(),
            'pending' => Donasi::where('status', 'pending')->count(),
            'failed' => Donasi::where('status', 'failed')->count(),
            'expired' => Donasi::where('status', 'expired')->count(),
            'total_amount' => Donasi::where('status', 'success')->sum('jumlah'),
            'today_success' => Donasi::where('status', 'success')
                ->whereDate('created_at', Carbon::today())
                ->sum('jumlah'),
            'this_month_success' => Donasi::where('status', 'success')
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('jumlah'),
        ];
        
        return view('admin.donasi.index', compact('donasis', 'stats'));
    }
    
    public function checkPending()
    {
        $pendingCount = Donasi::where('status', 'pending')->count();
        
        return response()->json([
            'has_new' => $pendingCount > 0,
            'count' => $pendingCount
        ]);
    }
    
    public function show(Donasi $donasi)
    {
        $donasi->load(['kampanye', 'user', 'datalansia']);
        return view('admin.donasi.show', compact('donasi'));
    }
    
    public function updateStatus(Request $request, Donasi $donasi)
    {
        $request->validate([
            'status' => 'required|in:pending,success,failed,expired'
        ]);
        
        $oldStatus = $donasi->status;
        $newStatus = $request->status;
        
        $donasi->update(['status' => $newStatus]);
        
        // Jika status berubah dari pending ke success
        if ($oldStatus != 'success' && $newStatus == 'success') {
            if ($donasi->kampanye) {
                $donasi->kampanye->increment('dana_terkumpul', $donasi->jumlah);
                $donasi->kampanye->increment('jumlah_donatur');
            }
        }
        // Jika status berubah dari success ke lainnya
        elseif ($oldStatus == 'success' && $newStatus != 'success') {
            if ($donasi->kampanye) {
                $donasi->kampanye->decrement('dana_terkumpul', $donasi->jumlah);
                $donasi->kampanye->decrement('jumlah_donatur');
            }
        }
        
        return back()->with('success', 'Status donasi berhasil diperbarui.');
    }
    
    public function export(Request $request)
{
    try {
        // Cek apakah ada data donasi
        $query = Donasi::query();
        
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $totalDonasi = $query->count();
        
        if ($totalDonasi === 0) {
            return redirect()->route('admin.donasi.index')
                ->with('warning', 'Tidak ada data donasi untuk diexport!');
        }
        
        $fileName = 'donasi_' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx';
        
        return Excel::download(new DonasiExport(
            $request->start_date,
            $request->end_date,
            $request->status
        ), $fileName);
        
    } catch (\Exception $e) {
        \Log::error('Export error: ' . $e->getMessage());
        
        return redirect()->route('admin.donasi.index')
            ->with('error', 'Gagal melakukan export: ' . $e->getMessage());
    }
}
    /**
     * Export data donasi dengan filter lengkap
     */
    public function exportFiltered(Request $request)
{
    try {
        $request->validate([
            'export_type' => 'required|in:all,success,pending,failed,expired',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);
        
        $status = $request->export_type == 'all' ? null : $request->export_type;
        
        // Cek apakah ada data
        $query = Donasi::query()
            ->when($status, function($query) use ($status) {
                return $query->where('status', $status);
            })
            ->when($request->start_date, function($query) use ($request) {
                return $query->whereDate('created_at', '>=', $request->start_date);
            })
            ->when($request->end_date, function($query) use ($request) {
                return $query->whereDate('created_at', '<=', $request->end_date);
            });
        
        $totalDonasi = $query->count();
        
        if ($totalDonasi === 0) {
            return redirect()->route('admin.donasi.index')
                ->with('warning', 'Tidak ada data donasi dengan filter yang dipilih!');
        }
        
        $fileName = 'donasi_' . $request->export_type . '_' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx';
        
        return Excel::download(new DonasiExport(
            $request->start_date,
            $request->end_date,
            $status
        ), $fileName);
        
    } catch (\Exception $e) {
        \Log::error('Export filtered error: ' . $e->getMessage());
        
        return back()->with('error', 'Gagal melakukan export: ' . $e->getMessage());
    }
}
    
    /**
     * Export ringkasan donasi (summary)
     */
    public function exportSummary(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);
        
        $fileName = 'ringkasan_donasi_' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx';
        
        return Excel::download(new DonasiSummaryExport(
            $request->start_date,
            $request->end_date
        ), $fileName);
    }
}