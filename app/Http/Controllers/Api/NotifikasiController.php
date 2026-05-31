<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function getNotifikasi()
    {
        $userId = Auth::id();

        $riwayatTransaksi = Transaksi::with('detail')
            ->where('ID_User', $userId)
            ->latest('tanggal_transaksi') 
            ->get();

        $dataNotifikasi = $riwayatTransaksi->map(function ($trx) {
            $judul = "Transaksi";
            $subJudul = $trx->detail ? $trx->detail->nama_penerima : "Pengguna TrustPay";
            $iconType = "send"; 
            $formattedNominal = "";

            // KOREKSI LOGIKA: TopUp/Add bertanda (+), Transfer/Send bertanda (-)
            if (in_array(strtolower($trx->jenis_transaksi), ['topup', 'add', 'topup ewallet'])) {
                $iconType = "add"; 
                $provider = $trx->detail ? $trx->detail->ewallet_tujuan : "E-Wallet";
                $judul = $provider . " - " . $subJudul; 
                $formattedNominal = "+ Rp " . number_format($trx->nominal, 0, ',', '.'); // Berubah jadi +
            } 
            elseif (strtolower($trx->jenis_transaksi) === 'exchange') {
                $iconType = "send"; 
                $judul = "Exchange " . $trx->mata_uang . " - " . $subJudul; 
                $formattedNominal = $trx->mata_uang . " " . number_format($trx->nominal, 2, ',', '.');
            } 
            else { 
                $iconType = "send";
                $bank = $trx->detail ? $trx->detail->bank_tujuan : "Bank";
                $judul = $bank . " - " . $subJudul; 
                $formattedNominal = "- Rp " . number_format($trx->nominal, 0, ',', '.');
            }

            return [
                'id_transaksi'      => $trx->ID_Transaksi,
                'jenis_transaksi'   => $trx->jenis_transaksi,
                'status_transaksi'  => $trx->status_transaksi, 
                'icon_type'         => $iconType,            
                'display_judul'     => $judul,               
                'display_nominal'   => $formattedNominal,    
                'waktu_transaksi'   => $trx->tanggal_transaksi
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Data notifikasi riwayat transaksi berhasil dimuat.',
            'data' => $dataNotifikasi
        ], 200);
    }
}