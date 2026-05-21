<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Saldo;
use Illuminate\Http\Request;

class SaldoController extends Controller
{
    public function topUp(Request $request)
    {
        $request->validate([
            'jumlah' => 'required|numeric|min:1',
            'mata_uang' => 'nullable|string|max:3'
        ]);

        $userId = $request->user()->ID_User;
        $currency = $request->input('mata_uang', 'IDR');

        $saldo = Saldo::where('ID_User', $userId)
                      ->where('mata_uang', $currency)
                      ->first();

        if (!$saldo) {
            $saldo = Saldo::create([
                'ID_User' => $userId,
                'jumlah_saldo' => $request->jumlah,
                'mata_uang' => $currency
            ]);
        } else {
            $saldo->jumlah_saldo += $request->jumlah;
            $saldo->save();
        }

        return response()->json([
            'message' => 'Top Up berhasil',
            'data' => [
                'ID_User' => $saldo->ID_User,
                'total_saldo' => $saldo->jumlah_saldo,
                'mata_uang' => $saldo->mata_uang
            ]
        ], 200);
    }
}