<?php

namespace App\Http\Controllers;

use App\Models\DataPerawat;
use Illuminate\Http\Request;

class DataPerawatController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->get('search');

        $DataPerawat = DataPerawat::when($keyword, function ($query, $keyword) {
            return $query->where('nama', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%")
                        ->orWhere('no_hp', 'like', "%{$keyword}%")
                        ->orWhere('alamat', 'like', "%{$keyword}%");
        })->orderBy('created_at', 'desc')->paginate(10);

        // Kirim data ke view
        return view('admin.DataPerawat.index', compact('DataPerawat', 'keyword'));
    }

    public function create()
    {
        return view('admin.DataPerawat.tambah');
    }

    public function store(Request $request)
    {
        // Validasi data
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:DataPerawat,email|max:255',
            'no_hp' => 'required|string|max:15',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'alamat' => 'required|string',
            'catatan' => 'nullable|string'
        ]);

        DataPerawat::create($request->all());
        return redirect()->route('admin.DataPerawat.index')->with('success', 'Data perawat berhasil ditambahkan!');
    }

    public function show($id)
    {
        $perawat = DataPerawat::findOrFail($id);
        return view('admin.DataPerawat.detail', compact('perawat'));
    }

public function edit($id)
{
    $DataPerawat = DataPerawat::findOrFail($id); // Ubah $perawat menjadi $DataPerawat
    return view('admin.DataPerawat.edit', compact('DataPerawat'));
}
    public function update(Request $request, $id)
    {
        // Validasi data
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:DataPerawat,email,' . $id,
            'no_hp' => 'required|string|max:15',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'alamat' => 'required|string',
            'catatan' => 'nullable|string'
        ]);

        $DataPerawat = DataPerawat::findOrFail($id);
        $DataPerawat->update($request->all());
        return redirect()->route('admin.DataPerawat.index')->with('success', 'Data perawat berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $DataPerawat = DataPerawat::findOrFail($id);
        $DataPerawat->delete();
        return redirect()->route('admin.DataPerawat.index')->with('success', 'Data perawat berhasil dihapus!');
    }
}