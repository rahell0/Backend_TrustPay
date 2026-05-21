<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Saldo;
use App\Models\Pin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'nomor_hp' => 'required|string|unique:users,nomor_hp|regex:/^[0-9]{9,15}$/', // Validasi format nomor HP
            'password' => [
                'required',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/'
            ],
            'pin'      => 'required|digits:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()
            ], 422);
        }

        // 1. Buat User baru dengan nomor_hp
        $user = User::create([
            'username' => $request->username,
            'nomor_hp' => $request->nomor_hp,
            'password' => Hash::make($request->password),
        ]);

        // 2. Buat PIN otomatis
        Pin::create([
            'ID_User'  => $user->ID_User,
            'Kode_PIN' => Hash::make($request->pin)
        ]);

        // 3. Buat Saldo default IDR (Mendukung visualisasi Dashboard Rp 100jt kamu nanti)
        Saldo::create([
            'ID_User'      => $user->ID_User,
            'jumlah_saldo' => 0, // Nilai awal pendaftaran tetap 0, nanti di-TopUp lewat API Saldo
            'mata_uang'    => 'IDR'
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Register berhasil',
            'data'    => $user
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'nomor_hp' => 'required|string',
            'password' => 'required|string',
        ]);

        // Cari berdasarkan nomor_hp, bukan email
        $user = User::where('nomor_hp', $request->nomor_hp)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => false,
                'message' => 'Nomor HP atau password salah'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'  => true,
            'message' => 'Login berhasil',
            'token'   => $token,
            'user'    => $user
        ], 200);
    }

    // KOREKSI UTAMA: Modifikasi Profile API agar otomatis mengirim data Saldo ke Dashboard Figma kamu!
    public function profile(Request $request)
    {
        $user = $request->user();
        
        // Ambil data saldo milik user yang sedang login
        $saldo = Saldo::where('ID_User', $user->ID_User)->first();

        return response()->json([
            'status' => true,
            'user'   => $user,
            'dashboard_saldo' => [
                'jumlah_saldo' => $saldo ? $saldo->jumlah_saldo : 0,
                'mata_uang'    => $saldo ? $saldo->mata_uang : 'IDR',
                'format_rupiah' => 'Rp ' . number_format($saldo ? $saldo->jumlah_saldo : 0, 2, ',', '.')
            ]
        ], 200);
    }
}