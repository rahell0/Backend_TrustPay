<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    /**
     * Mengambil daftar riwayat transaksi untuk halaman Notifikasi TrustPay
     */
    public function getNotifikasi()
    {
        // 1. Ambil ID User nasabah yang sedang login
        $userId = Auth::id();

        // 2. Ambil semua data transaksi milik user tersebut beserta detail tujuannya
        // Diurutkan dari yang paling terbaru (latest) agar riwayat teranyar muncul di paling atas
        $riwayatTransaksi = Transaksi::with('detail')
            ->where('ID_User', $userId)
            ->latest('tanggal_transaksi') 
            ->get();

        // 3. Modifikasi data agar siap dibaca oleh struktur UI di Figma
        $dataNotifikasi = $riwayatTransaksi->map(function ($trx) {
            
            $judul = "Transaksi";
            $subJudul = $trx->detail ? $trx->detail->nama_penerima : "Pengguna TrustPay";
            $iconType = "send"; // Default ikon panah kirim (>)
            $formattedNominal = "";

            // Deteksi Kategori untuk menentukan Judul Tampilan & Ikon
            if (in_array(strtolower($trx->jenis_transaksi), ['topup', 'add'])) {
                $iconType = "add"; // Menggunakan ikon tambah (+) di frontend
                $provider = $trx->detail ? $trx->detail->ewallet_tujuan : "E-Wallet";
                $judul = $provider . " - " . $subJudul; // Contoh: "ShopeePay - Angeliqia V.G Pardosi"
                $formattedNominal = "- Rp " . number_format($trx->nominal, 0, ',', '.'); // Sesuai tampilan Figma
            } 
            elseif (strtolower($trx->jenis_transaksi) === 'exchange') {
                $iconType = "send"; // Menggunakan ikon panah (>)
                $judul = "Exchange " . $trx->mata_uang . " - " . $subJudul; // Contoh: "Exchange USD - Angeliqia V.G Pardosi"
                
                // Jika transaksi exchange, sesuaikan format mata uangnya (Contoh: IDR 1.015.000,00)
                $formattedNominal = $trx->mata_uang . " " . number_format($trx->nominal, 2, ',', '.');
            } 
            else { // Kasus Transfer / SEND Nasional
                $iconType = "send";
                $bank = $trx->detail ? $trx->detail->bank_tujuan : "Bank";
                $judul = $bank . " - " . $subJudul; // Contoh: "Bank Mandiri - Angeliqia V.G Pardosi"
                $formattedNominal = "- Rp " . number_format($trx->nominal, 0, ',', '.');
            }

            return [
                'id_transaksi'      => $trx->ID_Transaksi,
                'jenis_transaksi'   => $trx->jenis_transaksi,
                'status_transaksi'  => $trx->status_transaksi, // sukses / pending / gagal
                'icon_type'         => $iconType,            // 'add' atau 'send' untuk dibaca frontend
                'display_judul'     => $judul,               // Tulisan tebal di kiri
                'display_nominal'   => $formattedNominal,    // Tulisan di kanan
                'waktu_transaksi'   => $trx->tanggal_transaksi
            ];
        });

        // 4. Kirim respons JSON ke frontend
        return response()->json([
            'status' => true,
            'message' => 'Data notifikasi riwayat transaksi berhasil dimuat.',
            'data' => $dataNotifikasi
        ], 200);
    }
}