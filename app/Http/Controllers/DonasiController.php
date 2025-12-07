<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Donasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        $donasis = Donasi::with(['kampanye', 'user'])
            ->where('status', 'success')
            ->when($request->start_date, function($query) use ($request) {
                return $query->whereDate('created_at', '>=', $request->start_date);
            })
            ->when($request->end_date, function($query) use ($request) {
                return $query->whereDate('created_at', '<=', $request->end_date);
            })
            ->get();
            
        // Logic untuk export Excel atau PDF
        // ...
    }
}