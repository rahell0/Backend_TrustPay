<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PinController extends Controller
{
    public function buatPin(Request $request)
    {
        $request->validate([
            'Kode_PIN' => 'required|digits:6'
        ]);

        $userId = $request->user()->ID_User;
        $cekPin = Pin::where('ID_User', $userId)->first();

        if ($cekPin) {
            return response()->json(['message' => 'PIN sudah pernah dibuat'], 400);
        }

        $pin = Pin::create([
            'ID_User' => $userId,
            'Kode_PIN' => Hash::make($request->Kode_PIN)
        ]);

        return response()->json([
            'message' => 'PIN berhasil dibuat',
            'data' => ['ID_User' => $pin->ID_User]
        ], 201);
    }
}