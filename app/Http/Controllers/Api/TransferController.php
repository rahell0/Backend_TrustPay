<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Saldo;
use App\Models\Pin;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TransferController extends Controller
{
    public function transferNasional(Request $request)
    {
        $request->validate([
            'bank_tujuan' => 'required',
            'nama_penerima' => 'required',
            'nominal' => 'required|numeric|min:1',
            'Kode_PIN' => 'required|digits:6'
        ]);

        $userId = $request->user()->ID_User;

        $pin = Pin::where('ID_User', $userId)->first();
        if (!$pin || !Hash::check($request->Kode_PIN, $pin->Kode_PIN)) {
            return response()->json(['message' => 'PIN salah'], 401);
        }

        $saldo = Saldo::where('ID_User', $userId)->where('mata_uang', 'IDR')->first();
        if (!$saldo || $saldo->jumlah_saldo < $request->nominal) {
            return response()->json(['message' => 'Saldo tidak cukup'], 400);
        }

        $saldo->jumlah_saldo -= $request->nominal;
        $saldo->save();

        $transaksi = Transaksi::create([
            'ID_User' => $userId,
            'ID_Admin' => null,
            'jenis_transaksi' => 'Transfer',
            'nominal' => $request->nominal,
            'mata_uang' => 'IDR',
            'status_transaksi' => 'disetujui',
            'tanggal_transaksi' => now()
        ]);

        DetailTransaksi::create([
            'ID_Transaksi' => $transaksi->ID_Transaksi,
            'bank_tujuan' => $request->bank_tujuan,
            'ewallet_tujuan' => null,
            'nama_penerima' => $request->nama_penerima,
            'negara_tujuan' => null
        ]);

        return response()->json([
            'message' => 'Transfer berhasil',
            'data' => [
                'ID_Transaksi' => $transaksi->ID_Transaksi,
                'sisa_saldo' => $saldo->jumlah_saldo
            ]
        ], 200);
    }

    public function riwayatTransaksi(Request $request)
    {
        $userId = $request->user()->ID_User;
        $transaksi = Transaksi::where('ID_User', $userId)->orderBy('created_at', 'desc')->get();

        if ($transaksi->isEmpty()) {
            return response()->json(['message' => 'Belum ada transaksi'], 404);
        }

        return response()->json([
            'message' => 'Riwayat transaksi ditemukan',
            'data' => $transaksi
        ], 200);
    }

    public function transferInternasional(Request $request)
    {
        $request->validate([
            'nama_penerima' => 'required',
            'negara_tujuan' => 'required',
            'mata_uang' => 'required|in:USD,EUR',
            'nominal' => 'required|numeric|min:1',
            'Kode_PIN' => 'required|digits:6'
        ]);

        $userId = $request->user()->ID_User;

        $pin = Pin::where('ID_User', $userId)->first();
        if (!$pin || !Hash::check($request->Kode_PIN, $pin->Kode_PIN)) {
            return response()->json(['message' => 'PIN salah'], 401);
        }

        // Mencari saldo asing milik user (misal USD atau EUR)
        $saldo = Saldo::where('ID_User', $userId)->where('mata_uang', $request->mata_uang)->first();
        if (!$saldo || $saldo->jumlah_saldo < $request->nominal) {
            return response()->json(['message' => 'Saldo valas tidak cukup / belum tersedia'], 400);
        }

        $saldo->jumlah_saldo -= $request->nominal;
        $saldo->save();

        $transaksi = Transaksi::create([
            'ID_User' => $userId,
            'ID_Admin' => null,
            'jenis_transaksi' => 'Internasional',
            'nominal' => $request->nominal,
            'mata_uang' => $request->mata_uang,
            'status_transaksi' => 'disetujui',
            'tanggal_transaksi' => now()
        ]);

        DetailTransaksi::create([
            'ID_Transaksi' => $transaksi->ID_Transaksi,
            'bank_tujuan' => null,
            'ewallet_tujuan' => null,
            'nama_penerima' => $request->nama_penerima,
            'negara_tujuan' => $request->negara_tujuan
        ]);

        return response()->json([
            'message' => 'Transfer internasional berhasil',
            'data' => [
                'ID_Transaksi' => $transaksi->ID_Transaksi,
                'mata_uang' => $request->mata_uang,
                'sisa_saldo' => $saldo->jumlah_saldo
            ]
        ], 200);
    }
}