<?php

namespace App\Http\Controllers;

use App\Models\DataPerawat;
use Illuminate\Http\Request;

class DataPerawatController extends Controller
{
    public function index(Request $request)
    {
        // Ambil nilai keyword dari input pencarian (GET)
        $keyword = $request->get('search');

        // Query pencarian
        $DataPerawat = DataPerawat::when($keyword, function ($query, $keyword) {
            return $query->where('nama', 'like', "%{$keyword}%");
        })->paginate(10);

        // Kirim data ke view
        return view('admin.dataperawat.index', compact('DataPerawat', 'keyword'));
    }

    public function create()
    {
        return view('admin.dataperawat.tambah');
    }

    public function store(Request $request)
    {
        DataPerawat::create($request->all());
        return redirect()->route('admin.dataperawat.index')->with('success', 'Data perawat berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $DataPerawat = DataPerawat::findOrFail($id);
        return view('admin.dataperawat.edit', compact('DataPerawat'));
    }

    public function update(Request $request, $id)
    {
        $DataPerawat = DataPerawat::findOrFail($id);
        $DataPerawat->update($request->all());
        return redirect()->route('admin.dataperawat.index')->with('success', 'Data perawat berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $DataPerawat = DataPerawat::findOrFail($id);
        $DataPerawat->delete();
        return redirect()->route('admin.dataperawat.index')->with('success', 'Data perawat berhasil dihapus!');
    }
}
