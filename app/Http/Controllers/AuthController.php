<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle Login API
     */
    public function login(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // 2. Cari User Berdasarkan Username beserta data Company-nya
        $user = User::with('company')->where('username', $request->username)->first();

        // 3. Cek apakah user ada dan password-nya benar
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'rc' => 400,
                'rm' => 'Username atau password salah.'
            ], 403);
        }

        // 4. Cek apakah Perusahaan/SaaS tempat dia bekerja sedang aktif
        if ($user->company && !$user->company->is_active) {
            return response()->json([
                'rc' => 400,
                'rm' => 'Akses diblokir. Perusahaan Anda sudah tidak aktif.'
            ], 403);
        }

        // 5. Generate Token Sanctum (Kita simpan kemampuan/role di dalam token nama)
        $token = $user->createToken($user->role . '_token')->plainTextToken;

        // 6. Kembalikan Response JSON untuk Frontend (Next.js)
        return response()->json([
            'rc' => 200,
            'rm' => 'Login berhasil.',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'fullname' => $user->fullname,
                    'email' => $user->email,
                    'role' => $user->role,
                    'company_id' => $user->company_id,
                    'company_name' => $user->company?->name,
                ]
            ]
        ], 200);
    }

    /**
     * Handle Logout API
     */
    public function logout(Request $request)
    {
        // Hapus token yang sedang digunakan saat ini
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'rc' => 200,
            'rm' => 'Logout berhasil'
        ], 200);
    }

    /**
     * Get Authenticated User Data
     */
    public function profile(Request $request)
    {
        $user = $request->user()->load('company');

        return response()->json([
            'rc' => 200,
            'rm' => 'Profile berhasil diambil.',
            'data' => [
                'id' => $user->id,
                'username' => $user->username,
                'fullname' => $user->fullname,
                'email' => $user->email,
                'role' => $user->role,
                'company_id' => $user->company_id,
                'company_name' => $user->company?->name,
            ]
        ], 200);
    }
}