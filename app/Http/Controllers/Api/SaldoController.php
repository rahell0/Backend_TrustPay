<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Saldo;
use Illuminate\Http\Request;

class SaldoController extends Controller
{
    public function topUp(Request $request)
    {
    
        // VALIDASI
        $request->validate([

            'ID_User' => 'required',

            'mata_uang' => 'required|in:IDR,USD,EUR',

            'jumlah' => 'required|numeric|min:1'

        ]);

        // CEK SALDO
        $saldo = Saldo::where('ID_User', $request->ID_User)
                        ->where('mata_uang', $request->mata_uang)
                        ->first();

        // JIKA BELUM ADA
        if (!$saldo) {

            $saldo = Saldo::create([

                'ID_User' => $request->ID_User,

                'mata_uang' => $request->mata_uang,

                'jumlah_saldo' => $request->jumlah

            ]);

        } else {

            // TAMBAH SALDO
            $saldo->jumlah_saldo += $request->jumlah;

            $saldo->save();
        }

        // RESPONSE
        return response()->json([

            'message' => 'Top Up berhasil',

            'data' => [

                'ID_User' => $saldo->ID_User,

                'mata_uang' => $saldo->mata_uang,

                'total_saldo' => $saldo->jumlah_saldo

            ]

        ], 200);
    }
    public function cekSaldo($id)
{
    $saldo = Saldo::where('ID_User', $id)->get();

    if ($saldo->isEmpty()) {

        return response()->json([

            'message' => 'Saldo tidak ditemukan'

        ], 404);
    }

    return response()->json([

        'message' => 'Data saldo ditemukan',

        'data' => $saldo

    ], 200);
  }
}