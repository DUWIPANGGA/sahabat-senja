<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthMobileController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(['admin', 'perawat', 'keluarga'])],
            'no_telepon' => 'nullable|string|max:15',
            'alamat' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'no_telepon' => $request->no_telepon,
                'alamat' => $request->alamat,
            ]);

            $token = $user->createToken('mobile-app-token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Registrasi berhasil',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'no_telepon' => $user->no_telepon,
                        'alamat' => $user->alamat,
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Registrasi gagal',
                'error' => $e->getMessage()
            ], 500);
        }
    }
     public function loginWithGoogle(Request $request)
    {
        Log::info('Google Login Request:', $request->all());

        $validator = Validator::make($request->all(), [
            'firebase_uid' => 'required|string',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255',
            'profile_photo' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed:', $validator->errors()->toArray());
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Cari user berdasarkan firebase_uid
            $user = User::where('firebase_uid', $request->firebase_uid)->first();
            Log::info('User found by firebase_uid:', ['user' => $user]);

            if ($user) {
                // User sudah ada, lakukan login
                $token = $user->createToken('mobile-app-token')->plainTextToken;
                Log::info('Existing user login successful');

                return response()->json([
                    'status' => 'success',
                    'message' => 'Login Google berhasil',
                    'data' => [
                        'user' => [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'role' => $user->role,
                            'firebase_uid' => $user->firebase_uid,
                            'profile_photo' => $user->profile_photo,
                            'no_telepon' => $user->no_telepon,
                            'alamat' => $user->alamat,
                        ],
                        'token' => $token,
                        'token_type' => 'Bearer'
                    ]
                ], 200);
            }

            // Jika user belum ada, cek jika email sudah ada di sistem
            if ($request->filled('email')) {
                $existingUser = User::where('email', $request->email)->first();
                Log::info('Existing user by email:', ['user' => $existingUser]);
                
                if ($existingUser) {
                    // Update user yang sudah ada dengan firebase_uid
                    $existingUser->update([
                        'firebase_uid' => $request->firebase_uid,
                        'profile_photo' => $request->profile_photo,
                    ]);

                    $token = $existingUser->createToken('mobile-app-token')->plainTextToken;
                    Log::info('Updated existing user with firebase_uid');

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Login Google berhasil',
                        'data' => [
                            'user' => [
                                'id' => $existingUser->id,
                                'name' => $existingUser->name,
                                'email' => $existingUser->email,
                                'role' => $existingUser->role,
                                'firebase_uid' => $existingUser->firebase_uid,
                                'profile_photo' => $existingUser->profile_photo,
                                'no_telepon' => $existingUser->no_telepon,
                                'alamat' => $existingUser->alamat,
                            ],
                            'token' => $token,
                            'token_type' => 'Bearer'
                        ]
                    ], 200);
                }
            }

            // Buat user baru dengan role 'keluarga'
            Log::info('Creating new user with Google login');
            
            $user = User::create([
                'firebase_uid' => $request->firebase_uid,
                'name' => $request->name ?? 'User Google',
                'email' => $request->email ?? $this->generateTemporaryEmail($request->firebase_uid),
                'password' => Hash::make(Str::random(16)), // Generate random password
                'role' => 'keluarga',
                'profile_photo' => $request->profile_photo,
            ]);

            Log::info('New user created:', ['user_id' => $user->id]);

            $token = $user->createToken('mobile-app-token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Registrasi dengan Google berhasil',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'firebase_uid' => $user->firebase_uid,
                        'profile_photo' => $user->profile_photo,
                        'no_telepon' => $user->no_telepon,
                        'alamat' => $user->alamat,
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Google login error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Login dengan Google gagal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate temporary email untuk user Google
     */
    private function generateTemporaryEmail($firebaseUid)
    {
        return 'google_' . substr($firebaseUid, 0, 10) . '@temporary.com';
    }

    /**
     * Login Google untuk Perawat
     */
    public function perawatLoginWithGoogle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firebase_uid' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Cari user perawat berdasarkan firebase_uid
            $user = User::where('firebase_uid', $request->firebase_uid)
                       ->where('role', 'perawat')
                       ->first();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Akun perawat tidak ditemukan. Silakan daftar terlebih dahulu.'
                ], 404);
            }

            // Buat token untuk perawat
            $token = $user->createToken('mobile-app-token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Login perawat dengan Google berhasil',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'firebase_uid' => $user->firebase_uid,
                        'profile_photo' => $user->profile_photo,
                        'no_telepon' => $user->no_telepon,
                        'alamat' => $user->alamat,
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Login perawat dengan Google gagal',
                'error' => $e->getMessage()
            ], 500);
        }
    }
public function perawatLogin(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|string|email',
        'password' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'message' => 'Validasi gagal',
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        // Cari user dengan role perawat
        $user = User::where('email', $request->email)
                    ->where('role', 'perawat')
                    ->first();

        // Cek jika user tidak ditemukan atau password salah
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email atau password salah'
            ], 401);
        }

        // Buat token Sanctum
        $token = $user->createToken('mobile-app-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login perawat berhasil',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'no_telepon' => $user->no_telepon,
                    'alamat' => $user->alamat,
                ],
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Login gagal',
            'error' => $e->getMessage()
        ], 500);
    }
}
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Cari user berdasarkan email
            $user = User::where('email', $request->email)->first();

            // Cek jika user tidak ditemukan atau password salah
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Email atau password salah'
                ], 401);
            }

            // Buat token Sanctum
            $token = $user->createToken('mobile-app-token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Login berhasil',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'no_telepon' => $user->no_telepon,
                        'alamat' => $user->alamat,
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Login gagal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            // Revoke current token
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Logout berhasil'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Logout gagal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function user(Request $request)
    {
        try {
            $user = $request->user();

            return response()->json([
                'status' => 'success',
                'message' => 'Data user berhasil diambil',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'no_telepon' => $user->no_telepon,
                        'alamat' => $user->alamat,
                        'created_at' => $user->created_at,
                        'updated_at' => $user->updated_at,
                    ]
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Optional: Method untuk update profile
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'no_telepon' => 'nullable|string|max:15',
            'alamat' => 'nullable|string|max:500',
            'current_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updateData = [];
            if ($request->filled('name')) $updateData['name'] = $request->name;
            if ($request->filled('email')) $updateData['email'] = $request->email;
            if ($request->filled('no_telepon')) $updateData['no_telepon'] = $request->no_telepon;
            if ($request->filled('alamat')) $updateData['alamat'] = $request->alamat;

            // Update password jika ada
            if ($request->filled('current_password') && $request->filled('new_password')) {
                if (!Hash::check($request->current_password, $user->password)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Password saat ini tidak sesuai'
                    ], 400);
                }
                $updateData['password'] = Hash::make($request->new_password);
            }

            $user->update($updateData);

            return response()->json([
                'status' => 'success',
                'message' => 'Profil berhasil diperbarui',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'no_telepon' => $user->no_telepon,
                        'alamat' => $user->alamat,
                    ]
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui profil',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}