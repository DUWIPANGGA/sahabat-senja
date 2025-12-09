<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Mendapatkan data user yang sedang login
     */
    public function getProfile(Request $request)
    {
        try {
            $user = $request->user();
            
            return response()->json([
                'success' => true,
                'message' => 'Profile berhasil diambil',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'no_telepon' => $user->no_telepon,
                    'alamat' => $user->alamat,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update profile user
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = $request->user();
            
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
                'no_telepon' => 'sometimes|string|max:15',
                'alamat' => 'sometimes|string|max:500',
            ], [
                'email.unique' => 'Email sudah digunakan oleh user lain',
                'name.max' => 'Nama maksimal 255 karakter',
                'no_telepon.max' => 'Nomor telepon maksimal 15 digit',
                'alamat.max' => 'Alamat maksimal 500 karakter',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Update data
            $dataToUpdate = [];
            
            if ($request->has('name')) {
                $dataToUpdate['name'] = $request->name;
            }
            
            if ($request->has('email') && $request->email !== $user->email) {
                $dataToUpdate['email'] = $request->email;
            }
            
            if ($request->has('no_telepon')) {
                $dataToUpdate['no_telepon'] = $request->no_telepon;
            }
            
            if ($request->has('alamat')) {
                $dataToUpdate['alamat'] = $request->alamat;
            }
            
            // Jika ada data yang diupdate
            if (!empty($dataToUpdate)) {
                $user->update($dataToUpdate);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Profile berhasil diperbarui',
                    'data' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'no_telepon' => $user->no_telepon,
                        'alamat' => $user->alamat,
                        'updated_at' => $user->updated_at,
                    ]
                ], 200);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data yang diperbarui'
            ], 400);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update password user
     */
    public function updatePassword(Request $request)
    {
        try {
            $user = $request->user();
            
            $validator = Validator::make($request->all(), [
                'current_password' => 'required|string',
                'new_password' => [
                    'required',
                    'string',
                    'confirmed',
                    Password::min(8)
                        ->mixedCase()
                        ->numbers()
                        ->symbols()
                        ->uncompromised(),
                ],
                'new_password_confirmation' => 'required|string',
            ], [
                'current_password.required' => 'Password saat ini harus diisi',
                'new_password.required' => 'Password baru harus diisi',
                'new_password.confirmed' => 'Konfirmasi password tidak cocok',
                'new_password.min' => 'Password minimal 8 karakter',
                'new_password.mixed' => 'Password harus mengandung huruf besar dan kecil',
                'new_password.numbers' => 'Password harus mengandung angka',
                'new_password.symbols' => 'Password harus mengandung simbol',
                'new_password_confirmation.required' => 'Konfirmasi password harus diisi',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Cek password saat ini
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password saat ini salah',
                    'errors' => ['current_password' => ['Password saat ini tidak valid']]
                ], 422);
            }
            
            // Update password
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Password berhasil diperbarui'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui password',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update profile picture (jika ada kebutuhan upload foto)
     */
    public function updateProfilePicture(Request $request)
    {
        try {
            $user = $request->user();
            
            $validator = Validator::make($request->all(), [
                'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ], [
                'profile_picture.required' => 'Foto profil harus diunggah',
                'profile_picture.image' => 'File harus berupa gambar',
                'profile_picture.mimes' => 'Format gambar yang diperbolehkan: jpeg, png, jpg, gif',
                'profile_picture.max' => 'Ukuran gambar maksimal 2MB',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Simpan gambar
            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                $filename = 'profile_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                
                // Simpan ke storage
                $path = $file->storeAs('profiles', $filename, 'public');
                
                // Update path gambar di database
                $user->update([
                    'profile_picture' => $path
                ]);
                
                $fullUrl = Storage::url($path);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Foto profil berhasil diperbarui',
                    'data' => [
                        'profile_picture' => $fullUrl,
                        'profile_picture_url' => asset($fullUrl)
                    ]
                ], 200);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada file yang diunggah'
            ], 400);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui foto profil',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hapus profile picture
     */
    public function removeProfilePicture(Request $request)
    {
        try {
            $user = $request->user();
            
            if ($user->profile_picture) {
                // Hapus file dari storage
                Storage::disk('public')->delete($user->profile_picture);
                
                // Update database
                $user->update(['profile_picture' => null]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Foto profil berhasil dihapus'
                ], 200);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada foto profil yang tersedia'
            ], 400);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus foto profil',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}