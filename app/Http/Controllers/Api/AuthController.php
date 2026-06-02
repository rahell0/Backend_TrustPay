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
            'nomor_hp' => 'required|string|unique:users,nomor_hp|regex:/^[0-9]{9,15}$/',
            'password' => [
                'required',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[@$%]/'
            ],
            'pin'      => 'required|digits:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'username' => $request->username,
            'nomor_hp' => $request->nomor_hp,
            'password' => Hash::make($request->password),
            'role'     => 'user',
            'status_operasional' => 'Aktif'
        ]);

        Pin::create([
            'ID_User'  => $user->ID_User,
            'Kode_PIN' => Hash::make($request->pin)
        ]);

        Saldo::create([
            'ID_User'      => $user->ID_User,
            'jumlah_saldo' => 10000000, // Rp 10.000.000 saldo awal simulasi
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
            'user'    => [
                'ID_User'  => $user->ID_User,
                'username' => $user->username,
                'nomor_hp' => $user->nomor_hp,
                'role'     => $user->role,
                'status'   => $user->status_operasional
            ]
        ], 200);
    }

    public function profile(Request $request)
    {
        $user = $request->user();
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

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Logout berhasil. Sesi token Anda telah dihapus.'
        ], 200);
    }
}