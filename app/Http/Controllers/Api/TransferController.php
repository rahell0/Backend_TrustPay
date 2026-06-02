<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Saldo;
use App\Models\Pin;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
// IMPORT MODEL SIMULASI BARU
use App\Models\BankAccount;
use App\Models\ValasAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class TransferController extends Controller
{
    public function transferNasional(Request $request)
    {
        // 1. VALIDASI INPUT SESUAI FIGMA
        $validator = Validator::make($request->all(), [
            'bank_tujuan'    => 'required|in:Mandiri,BCA,BRI', 
            'nomor_rekening' => 'required|numeric',
            'nominal'        => 'required|numeric|min:20000', // Sesuai teks Figma: "Minimal transfer Rp 20.000"
            'Kode_PIN'       => 'required|digits:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()], 422);
        }

        $userId = $request->user()->ID_User;
        $biayaAdmin = 1000; // Sesuai visual konfirmasi Figma Nasional
        $totalPotong = $request->nominal + $biayaAdmin;

        // 2. VERIFIKASI PIN
        $pin = Pin::where('ID_User', $userId)->first();
        if (!$pin || !Hash::check($request->Kode_PIN, $pin->Kode_PIN)) {
            return response()->json(['status' => false, 'message' => 'PIN salah'], 401);
        }

        // 3. VALIDASI REKENING BANK TUJUAN DI DATABASE VENDOR
        $bankTarget = BankAccount::where('nama_bank', $request->bank_tujuan)
                                 ->where('nomor_rekening', $request->nomor_rekening)
                                 ->first();

        if (!$bankTarget) {
            return response()->json(['status' => false, 'message' => 'Nomor rekening atau Bank tujuan tidak ditemukan'], 404);
        }

        // 4. CEK SALDO IDR TRUSTPAY
        $saldo = Saldo::where('ID_User', $userId)->where('mata_uang', 'IDR')->first();
        if (!$saldo || $saldo->jumlah_saldo < $totalPotong) {
            return response()->json(['status' => false, 'message' => 'Saldo TrustPay tidak cukup untuk nominal + admin'], 400);
        }

        // 5. EKSEKUSI MUTASI DANA
        $saldo->jumlah_saldo -= $totalPotong;
        $saldo->save();

        $bankTarget->saldo += $request->nominal; // Dana masuk ke rekening simulasi bank tujuan
        $bankTarget->save();

        // 6. SIMPAN TRANSAKSI MASTER
        $transaksi = Transaksi::create([
            'ID_User'           => $userId,
            'ID_Admin'          => null,
            'jenis_transaksi'   => 'Send-Nasional',
            'nominal'           => $request->nominal,
            'biaya_admin'       => $biayaAdmin,
            'mata_uang'         => 'IDR',
            'status_transaksi'  => 'success',
            'tanggal_transaksi' => now()
        ]);

        // 7. SIMPAN DETAIL TRANSAKSI
        DetailTransaksi::create([
            'ID_Transaksi'   => $transaksi->ID_Transaksi,
            'bank_tujuan'    => $request->bank_tujuan,
            'ewallet_tujuan' => null,
            'nama_penerima'  => $bankTarget->nama_pemilik, // Otomatis ditarik dari DB Bank
            'negara_tujuan'  => 'Indonesia'
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Transfer nasional berhasil dilakukan',
            'receipt' => [
                'tanggal_waktu'   => $transaksi->created_at->translatedFormat('d F Y, H:i:s'),
                'jenis_layanan'   => 'Send-Nasional',
                'bank_tujuan'     => $request->bank_tujuan,
                'nomor_rekening'  => $request->nomor_rekening,
                'nama_penerima'   => $bankTarget->nama_pemilik,
                'nominal'         => $transaksi->nominal,
                'biaya_admin'     => $biayaAdmin,
                'total_biaya'     => $totalPotong,
                'sisa_saldo_user' => $saldo->jumlah_saldo
            ]
        ], 200);
    }

    public function transferInternasional(Request $request)
    {
        // 1. VALIDASI INPUT DISESUAIKAN DENGAN FORM RUPIAH DI FIGMA
        $validator = Validator::make($request->all(), [
            'negara_tujuan'  => 'required|in:United States,Malaysia',
            'mata_uang'      => 'required|in:USD,MYR',
            'nomor_rekening' => 'required',
            'nominal_idr'    => 'required|numeric|min:10000', // Frontend mengirim nominal dasar Rupiah yang diketik user
            'Kode_PIN'       => 'required|digits:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()], 422);
        }

        $userId = $request->user()->ID_User;

        // 2. VERIFIKASI PIN
        $pin = Pin::where('ID_User', $userId)->first();
        if (!$pin || !Hash::check($request->Kode_PIN, $pin->Kode_PIN)) {
            return response()->json(['status' => false, 'message' => 'PIN salah'], 401);
        }

        // 3. CEK REKENING VALAS TARGET
        $valasTarget = ValasAccount::where('negara_tujuan', $request->negara_tujuan)
                                   ->where('mata_uang', $request->mata_uang)
                                   ->where('nomor_rekening', $request->nomor_rekening)
                                   ->first();

        if (!$valasTarget) {
            return response()->json(['status' => false, 'message' => 'Rekening internasional tidak terdaftar'], 404);
        }

        // 4. KALKULASI REVERSE (IDR ke VALAS) Sesuai Ringkasan Konversi UI
        $rate = ($request->mata_uang === 'USD') ? 0.0001 : 0.0003; // Mengikuti visual kurs figma Anda: 1 IDR = 0.0001 USD
        $hasilKonversiValas = $request->nominal_idr * $rate;

        // Komponen biaya administrasi dari figma
        $biayaAdmin = 25000; // Sesuai teks "Admin: 25.000 IDR" di page 10 UI Anda
        $totalBiayaPotongSaldo = $request->nominal_idr + $biayaAdmin;

        // 5. CEK SALDO RUPIAH USER
        $saldoIDR = Saldo::where('ID_User', $userId)->where('mata_uang', 'IDR')->first();
        if (!$saldoIDR || $saldoIDR->jumlah_saldo < $totalBiayaPotongSaldo) {
            return response()->json(['status' => false, 'message' => 'Saldo Rupiah Anda tidak mencukupi untuk total biaya potong saldo'], 400);
        }

        // 6. EKSEKUSI MUTASI
        $saldoIDR->jumlah_saldo -= $totalBiayaPotongSaldo;
        $saldoIDR->save();

        $valasTarget->saldo_valas += $hasilKonversiValas;
        $valasTarget->save();

        // 7. SIMPAN TRANSAKSI
        $transaksi = Transaksi::create([
            'ID_User'           => $userId,
            'jenis_transaksi'   => 'Send-Internasional',
            'nominal'           => $request->nominal_idr, 
            'biaya_admin'       => $biayaAdmin,
            'mata_uang'         => 'IDR',
            'status_transaksi'  => 'success',
            'tanggal_transaksi' => now()
        ]);

        DetailTransaksi::create([
            'ID_Transaksi'   => $transaksi->ID_Transaksi,
            'bank_tujuan'    => 'CrossBorder-Transfer',
            'ewallet_tujuan' => null,
            'nama_penerima'  => $valasTarget->nama_penerima,
            'negara_tujuan'  => $request->negara_tujuan
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Transfer internasional berhasil dilakukan',
            'receipt' => [
                'tanggal_waktu'     => now()->translatedFormat('d F Y, H:i:s'),
                'nominal_dikirim'   => 'Rp ' . number_format($request->nominal_idr, 0, ',', '.'),
                'kurs_berlaku'      => '1 IDR = ' . $rate . ' ' . $request->mata_uang,
                'hasil_valas'       => $hasilKonversiValas . ' ' . $request->mata_uang,
                'biaya_admin'       => 'Rp ' . number_format($biayaAdmin, 0, ',', '.'),
                'total_potong'      => 'Rp ' . number_format($totalBiayaPotongSaldo, 0, ',', '.'),
                'sisa_saldo'        => $saldoIDR->jumlah_saldo
            ]
        ], 200);
    }
}