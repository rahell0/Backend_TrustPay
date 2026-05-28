<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Saldo;
use App\Models\Pin;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
// IMPORT MODEL E-WALLET BARU
use App\Models\ShopeepayAccount;
use App\Models\GopayAccount;
use App\Models\DanaAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class EwalletController extends Controller
{
    public function topUpEwallet(Request $request)
    {
        // 1. VALIDASI INPUT (Variabel disesuaikan dengan kebutuhan UI Figma)
        $validator = Validator::make($request->all(), [
            'ewallet_tujuan' => 'required|in:Shopeepay,Gopay,Dana', // Pilihan terbatas sesuai tombol Figma
            'nomor_tujuan'   => 'required|regex:/^[0-9]{9,15}$/', // Menangkap input No. Telepon
            'nominal'        => 'required|numeric|min:10000', // Batas minimal top-up e-wallet umum
            'Kode_PIN'       => 'required|digits:6' // Input 6 digit PIN keamanan
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()
            ], 422);
        }

        $userId = $request->user()->ID_User; // Mendapatkan ID User dari proteksi Sanctum
        $biayaAdmin = 1000; // Biaya admin Rp 1.000 sesuai visual konfirmasi Figma
        $totalPotongSaldo = $request->nominal + $biayaAdmin;

        // 2. VERIFIKASI KEAMANAN PIN
        $pin = Pin::where('ID_User', $userId)->first();
        if (!$pin || !Hash::check($request->Kode_PIN, $pin->Kode_PIN)) {
            return response()->json([
                'status'  => false,
                'message' => 'PIN yang Anda masukkan salah'
            ], 401);
        }

        // 3. VERIFIKASI KEBERADAAN AKUN E-WALLET TARGET (Permintaan Dosen)
        $targetAccount = null;
        if ($request->ewallet_tujuan === 'Shopeepay') {
            $targetAccount = ShopeepayAccount::where('nomor_telepon', $request->nomor_tujuan)->first();
        } elseif ($request->ewallet_tujuan === 'Gopay') {
            $targetAccount = GopayAccount::where('nomor_telepon', $request->nomor_tujuan)->first();
        } elseif ($request->ewallet_tujuan === 'Dana') {
            $targetAccount = DanaAccount::where('nomor_telepon', $request->nomor_tujuan)->first();
        }

        // Jika nomor telepon target tidak ditemukan di database e-wallet terkait
        if (!$targetAccount) {
            return response()->json([
                'status'  => false,
                'message' => 'Nomor tujuan tidak terdaftar di platform ' . $request->ewallet_tujuan
            ], 404);
        }

        // 4. VERIFIKASI KECUKUPAN SALDO TRUSTPAY USER
        $saldo = Saldo::where('ID_User', $userId)->where('mata_uang', 'IDR')->first();
        if (!$saldo || $saldo->jumlah_saldo < $totalPotongSaldo) {
            return response()->json([
                'status'  => false,
                'message' => 'Saldo TrustPay Anda tidak mencukupi (Dibutuhkan nominal + biaya admin)'
            ], 400);
        }

        // 5. PROSES EKSEKUSI MUTASI DANA (POTONG & TAMBAH)
        // A. Potong saldo utama milik pengirim di TrustPay
        $saldo->jumlah_saldo -= $totalPotongSaldo;
        $saldo->save();

        // B. Tambah saldo penerima di akun platform E-Wallet tujuan (Simulasi mutasi antar-platform)
        $targetAccount->saldo += $request->nominal;
        $targetAccount->save();

        // 6. CATAT TRANSAKSI MASTER
        $transaksi = Transaksi::create([
            'ID_User'          => $userId,
            'ID_Admin'         => null,
            'jenis_transaksi'  => 'TopUp EWallet',
            'nominal'          => $request->nominal,
            'biaya_admin'      => $biayaAdmin,
            'mata_uang'        => 'IDR',
            'status_transaksi' => 'disetujui', // Langsung sukses karena ini simulasi lokal
        ]);

        // 7. CATAT DETAIL TRANSAKSI
        $detail = DetailTransaksi::create([
            'ID_Transaksi'   => $transaksi->ID_Transaksi,
            'bank_tujuan'    => null,
            'ewallet_tujuan' => $request->ewallet_tujuan,
            'nomor_tujuan'   => $request->nomor_tujuan,
            'nama_penerima'  => $targetAccount->nama_pemilik, // Nama otomatis ditarik dari database e-wallet!
            'negara_tujuan'  => null
        ]);

        // 8. RESPONSE API - Mengembalikan data utuh untuk keperluan Cetak Struk Sukses di Figma
        return response()->json([
            'status'  => true,
            'message' => 'Transaksi Berhasil dilakukan',
            'receipt' => [
                'tanggal_waktu'   => $transaksi->created_at->translatedFormat('d F Y, H:i:s'), // Format rapi: 09 April 2026, 13:58:25
                'jenis_layanan'   => $transaksi->jenis_transaksi,
                'platform_target' => $detail->ewallet_tujuan,
                'nomor_tujuan'    => $detail->nomor_tujuan,
                'nama_penerima'   => $detail->nama_penerima,
                'nominal_transfer'=> $transaksi->nominal,
                'biaya_admin'     => $transaksi->biaya_admin,
                'total_biaya'     => $totalPotongSaldo,
                'sisa_saldo_user' => $saldo->jumlah_saldo
            ]
        ], 200);
    }
}