<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\KampanyeDonasi;
use App\Models\Donasi;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class KampanyeDonasiController extends Controller
{
public function index(Request $request)
{
    $query = KampanyeDonasi::query();
    
    // Filter status
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }
    
    // Filter kategori
    if ($request->filled('kategori')) {
        $query->where('kategori', $request->kategori);
    }
    
    // Pencarian
    if ($request->filled('search')) {
        $query->where('judul', 'like', '%' . $request->search . '%');
    }
    
    $kampanyes = $query->latest()->paginate(10);
    
    // Stats untuk cards
    $totalKampanye = KampanyeDonasi::count();
    $kampanyeAktif = KampanyeDonasi::where('status', 'aktif')->count();
    $totalDana = KampanyeDonasi::sum('dana_terkumpul');
    $totalDonatur = Donasi::where('status', 'success')->count();
    
    return view('admin.kampanye.index', compact(
        'kampanyes',
        'totalKampanye',
        'kampanyeAktif',
        'totalDana',
        'totalDonatur'
    ));
}

public function create()
{
    $datalansia = \App\Models\DataLansia::all();
    return view('admin.kampanye.create', compact('datalansia'));
}

public function edit(KampanyeDonasi $kampanye)
{
    $datalansia = \App\Models\DataLansia::all();
    return view('admin.kampanye.edit', compact('kampanye', 'datalansia'));
}


    public function store(Request $request)
    {
        $request->merge([
        'target_dana' => preg_replace('/\D/', '', $request->target_dana) // bersihin titik/comma
    ]);
        $validated = $request->validate([
            'judul' => 'required|max:255',
            'deskripsi_singkat' => 'required|max:500',
            'deskripsi' => 'required',
            'target_dana' => 'required|numeric|min:100000',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'kategori' => 'required',
            'gambar' => 'nullable|image|max:2048',
            'thumbnail' => 'nullable|image|max:1024',
            'galeri.*' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,aktif,selesai,ditutup',
            'is_featured' => 'boolean',
            'datalansia_id' => 'nullable|exists:datalansia,id',
            'cerita_lengkap' => 'nullable',
            'terima_kasih_pesan' => 'nullable'
        ]);

        $validated['slug'] = Str::slug($request->judul) . '-' . Str::random(5);
        
        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('kampanye/gambar', 'public');
        }
        
        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('kampanye/thumbnail', 'public');
        }
        
        if ($request->hasFile('galeri')) {
            $galeri = [];
            foreach ($request->file('galeri') as $file) {
                $galeri[] = $file->store('kampanye/galeri', 'public');
            }
            $validated['galeri'] = json_encode($galeri);
        }
        
        KampanyeDonasi::create($validated);
        
        return redirect()->route('admin.kampanye.index')
            ->with('success', 'Kampanye donasi berhasil dibuat.');
    }


    public function update(Request $request, KampanyeDonasi $kampanye)
    {
        $validated = $request->validate([
            'judul' => 'required|max:255',
            'deskripsi_singkat' => 'required|max:500',
            'deskripsi' => 'required',
            'target_dana' => 'required|numeric|min:100000',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'kategori' => 'required',
            'gambar' => 'nullable|image|max:2048',
            'thumbnail' => 'nullable|image|max:1024',
            'galeri.*' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,aktif,selesai,ditutup',
            'is_featured' => 'boolean',
            'datalansia_id' => 'nullable|exists:datalansia,id',
            'cerita_lengkap' => 'nullable',
            'terima_kasih_pesan' => 'nullable'
        ]);
        
        if ($request->hasFile('gambar')) {
            if ($kampanye->gambar) {
                Storage::disk('public')->delete($kampanye->gambar);
            }
            $validated['gambar'] = $request->file('gambar')->store('kampanye/gambar', 'public');
        }
        
        if ($request->hasFile('thumbnail')) {
            if ($kampanye->thumbnail) {
                Storage::disk('public')->delete($kampanye->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('kampanye/thumbnail', 'public');
        }
        
        $kampanye->update($validated);
        
        return redirect()->route('admin.kampanye.index')
            ->with('success', 'Kampanye donasi berhasil diperbarui.');
    }

    public function destroy(KampanyeDonasi $kampanye)
    {
        if ($kampanye->gambar) {
            Storage::disk('public')->delete($kampanye->gambar);
        }
        if ($kampanye->thumbnail) {
            Storage::disk('public')->delete($kampanye->thumbnail);
        }
        
        $kampanye->delete();
        
        return redirect()->route('admin.kampanye.index')
            ->with('success', 'Kampanye donasi berhasil dihapus.');
    }

    public function show($id)
    {
        $kampanye = KampanyeDonasi::with(['donasis' => function($query) {
            $query->where('status', 'success')->latest();
        }])->findOrFail($id);
        
        return view('admin.kampanye.show', compact('kampanye'));
    }

    public function updateStatus(Request $request, KampanyeDonasi $kampanye)
    {
        $request->validate([
            'status' => 'required|in:draft,aktif,selesai,ditutup'
        ]);
        
        $kampanye->update(['status' => $request->status]);
        
        return back()->with('success', 'Status kampanye berhasil diperbarui.');
    }
}