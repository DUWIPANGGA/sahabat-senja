<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profile
     */
    public function index()
    {
        $user = Auth::user();
        return view('profile.index', compact('user'));
    }

    /**
     * Menampilkan form edit profile
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update data profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'no_telepon' => ['nullable', 'string', 'max:15'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'foto_profil' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        // Handle upload foto profil
        if ($request->hasFile('foto_profil')) {
            // Hapus foto lama jika ada
            if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }
            
            // Simpan foto baru
            $path = $request->file('foto_profil')->store('profile-photos', 'public');
            $validated['foto_profil'] = $path;
        }

        $user->update($validated);

        return redirect()->route('profile.index')
            ->with('success', 'Profile berhasil diperbarui!');
    }
public function uploadPhoto(Request $request)
    {
        $request->validate([
            'foto_profil' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $user = Auth::user();

        // Hapus foto lama jika ada
        if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
            Storage::disk('public')->delete($user->foto_profil);
        }

        // Simpan foto baru
        $path = $request->file('foto_profil')->store('profile-photos', 'public');
        $user->update(['foto_profil' => $path]);

        return redirect()->route('profile.index')
            ->with('success', 'Foto profil berhasil diupload!');
    }
    /**
     * Menampilkan form edit password
     */
    public function editPassword()
    {
        return view('profile.edit-password');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();

        // Cek password saat ini
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Password saat ini tidak sesuai.'
            ]);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('profile.index')
            ->with('success', 'Password berhasil diperbarui!');
    }

    /**
     * Menghapus foto profil
     */
    public function deletePhoto()
    {
        $user = Auth::user();
        
        if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
            Storage::disk('public')->delete($user->foto_profil);
            
            $user->update(['foto_profil' => null]);
            
            return back()->with('success', 'Foto profil berhasil dihapus!');
        }
        
        return back()->with('error', 'Foto profil tidak ditemukan!');
    }

    /**
     * Mendapatkan data user untuk API (jika diperlukan)
     */
    public function getProfile()
    {
        $user = Auth::user();
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Update profile via API
     */
    public function updateProfileApi(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'no_telepon' => ['sometimes', 'string', 'max:15'],
            'alamat' => ['sometimes', 'string', 'max:500'],
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profile berhasil diperbarui',
            'data' => $user
        ]);
    }

    /**
     * Update password via API
     */
    public function updatePasswordApi(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password saat ini tidak sesuai.'
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diperbarui!'
        ]);
    }

    /**
     * Upload foto profil via API
     */
    public function uploadPhotoApi(Request $request)
    {
        $request->validate([
            'foto_profil' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $user = Auth::user();

        // Hapus foto lama jika ada
        if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
            Storage::disk('public')->delete($user->foto_profil);
        }

        // Simpan foto baru
        $path = $request->file('foto_profil')->store('profile-photos', 'public');
        $user->update(['foto_profil' => $path]);

        return response()->json([
            'success' => true,
            'message' => 'Foto profil berhasil diupload',
            'data' => [
                'foto_profil' => $user->foto_profil,
                'foto_url' => Storage::disk('public')->url($path)
            ]
        ]);
    }
}