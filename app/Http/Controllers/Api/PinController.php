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
            'ID_User' => 'required',
            'Kode_PIN' => 'required|digits:6'
        ]);

        $cekPin = Pin::where('ID_User', $request->ID_User)->first();

        if ($cekPin) {
            return response()->json([
                'message' => 'PIN sudah pernah dibuat'
            ], 400);
        }

        $pin = Pin::create([
            'ID_User' => $request->ID_User,
            'Kode_PIN' => Hash::make($request->Kode_PIN)

        
        ]);

        return response()->json([
            'message' => 'PIN berhasil dibuat',
            'data' => [
                'ID_User' => $pin->ID_User
            ]
        ], 201);
    }
}