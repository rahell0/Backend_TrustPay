<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Saldo;
use App\Models\Pin;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EwalletController extends Controller
{
    public function topUpEwallet(Request $request)
    {
        // VALIDASI
        $request->validate([

            'ID_User' => 'required',

            'ewallet_tujuan' => 'required',

            'nama_penerima' => 'required',

            'nominal' => 'required|numeric|min:1',

            'Kode_PIN' => 'required|digits:6'

        ]);

        // CEK PIN
        $pin = Pin::where('ID_User', $request->ID_User)->first();

        if (!$pin || !Hash::check($request->Kode_PIN, $pin->Kode_PIN)) {

            return response()->json([

                'message' => 'PIN salah'

            ], 401);
        }

        // CEK SALDO
        $saldo = Saldo::where('ID_User', $request->ID_User)
                        ->where('mata_uang', 'IDR')
                        ->first();

        if (!$saldo || $saldo->jumlah_saldo < $request->nominal) {

            return response()->json([

                'message' => 'Saldo tidak cukup'

            ], 400);
        }

        // KURANGI SALDO
        $saldo->jumlah_saldo -= $request->nominal;

        $saldo->save();

        // SIMPAN TRANSAKSI
        $transaksi = Transaksi::create([

            'ID_User' => $request->ID_User,

            'ID_Admin' => null,

            'jenis_transaksi' => 'TopUp EWallet',

            'nominal' => $request->nominal,

            'mata_uang' => 'IDR',

            'status_transaksi' => 'disetujui',

            'tanggal_transaksi' => now()

        ]);

        // DETAIL TRANSAKSI
        DetailTransaksi::create([

            'ID_Transaksi' => $transaksi->ID_Transaksi,

            'bank_tujuan' => null,

            'ewallet_tujuan' => $request->ewallet_tujuan,

            'nama_penerima' => $request->nama_penerima,

            'negara_tujuan' => null

        ]);

        // RESPONSE
        return response()->json([

            'message' => 'Top Up E-Wallet berhasil',

            'data' => [

                'ID_Transaksi' => $transaksi->ID_Transaksi,

                'ewallet_tujuan' => $request->ewallet_tujuan,

                'sisa_saldo' => $saldo->jumlah_saldo

            ]

        ], 200);
    }
}