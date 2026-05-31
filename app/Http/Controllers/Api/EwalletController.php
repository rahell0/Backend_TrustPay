<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Saldo;
use App\Models\Pin;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\ShopeepayAccount;
use App\Models\GopayAccount;
use App\Models\DanaAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class EwalletController extends Controller
{
    public function topUpEwallet(Request $request)
    {
        // 1. VALIDASI INPUT 
        $validator = Validator::make($request->all(), [
            'ewallet_tujuan' => 'required|in:Shopeepay,Gopay,Dana', 
            'nomor_tujuan'   => 'required|regex:/^[0-9]{9,15}$/', 
            'nominal'        => 'required|numeric|min:10000', 
            'Kode_PIN'       => 'required|digits:6' 
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()
            ], 422);
        }

        $userId = $request->user()->ID_User; 
        $biayaAdmin = 1000; 
        $totalPotongSaldo = $request->nominal + $biayaAdmin;

        // 2. VERIFIKASI KEAMANAN PIN
        $pin = Pin::where('ID_User', $userId)->first();
        if (!$pin || !Hash::check($request->Kode_PIN, $pin->Kode_PIN)) {
            return response()->json([
                'status'  => false,
                'message' => 'PIN yang Anda masukkan salah'
            ], 401);
        }

        // 3. VERIFIKASI KEBERADAAN AKUN E-WALLET TARGET
        $targetAccount = null;
        if ($request->ewallet_tujuan === 'Shopeepay') {
            $targetAccount = ShopeepayAccount::where('nomor_telepon', $request->nomor_tujuan)->first();
        } elseif ($request->ewallet_tujuan === 'Gopay') {
            $targetAccount = GopayAccount::where('nomor_telepon', $request->nomor_tujuan)->first();
        } elseif ($request->ewallet_tujuan === 'Dana') {
            $targetAccount = DanaAccount::where('nomor_telepon', $request->nomor_tujuan)->first();
        }

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

        // 5. PROSES EKSEKUSI DENGAN TRY-CATCH & TRANSACTION YANG FIX
        try {
            // Jalankan transaksi database
            DB::transaction(function () use ($saldo, $totalPotongSaldo, $targetAccount, $request, $userId, $biayaAdmin) {

                // A. Potong saldo utama milik pengirim di TrustPay
                $saldo->jumlah_saldo -= $totalPotongSaldo;
                $saldo->save();

                // B. Tambah saldo penerima di akun platform E-Wallet tujuan
                $targetAccount->saldo += $request->nominal;
                $targetAccount->save();

                // C. Catat Transaksi Master
                $transaksi = Transaksi::create([
                    'ID_User'           => $userId,
                    'ID_Admin'          => null,
                    'jenis_transaksi'   => 'TopUp EWallet',
                    'nominal'           => $request->nominal,
                    'biaya_admin'       => $biayaAdmin,
                    'mata_uang'         => 'IDR',
                    'status_transaksi'  => 'success', 
                    'tanggal_transaksi' => now(),
                ]);

                // D. Catat Detail Transaksi
                DetailTransaksi::create([
                    'ID_Transaksi'   => $transaksi->ID_Transaksi,
                    'bank_tujuan'    => null,
                    'ewallet_tujuan' => $request->ewallet_tujuan,
                    'nomor_tujuan'   => $request->nomor_tujuan,
                    'nama_penerima'  => $targetAccount->nama_pemilik, 
                    'negara_tujuan'  => null
                ]);
            });

            // 6. RESPONSE SUKSES (Berada di dalam blok TRY setelah TRANSACTION selesai tanpa error)
            return response()->json([
                'status'  => true,
                'message' => 'Transaksi Berhasil dilakukan',
                'receipt' => [
                    'tanggal_waktu'   => now()->translatedFormat('d F Y, H:i:s'), 
                    'jenis_layanan'   => 'TopUp EWallet',
                    'platform_target' => $request->ewallet_tujuan,
                    'nomor_tujuan'    => $request->nomor_tujuan,
                    'nama_penerima'   => $targetAccount->nama_pemilik,
                    'nominal_transfer'=> $request->nominal,
                    'biaya_admin'     => $biayaAdmin,
                    'total_biaya'     => $totalPotongSaldo,
                    'sisa_saldo_user' => $saldo->fresh()->jumlah_saldo // Dijamin logis dan akurat
                ]
            ], 200);

        } catch (\Exception $e) {
            // Jika ada baris database yang gagal, otomasi ROLLBACK aktif dan tangkap error di sini
            return response()->json([
                'status'  => false,
                'message' => 'Sistem Error, Transaksi Dibatalkan Otomatis: ' . $e->getMessage()
            ], 500);
        }
    }
}