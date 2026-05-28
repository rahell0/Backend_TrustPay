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
            'bank_tujuan'    => 'required|in:BRI,BNI,Mandiri', 
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
            'mata_uang'         => 'IDR',
            'status_transaksi'  => 'disetujui',
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
        // 1. VALIDASI INPUT INTERNASIONAL SESUAI FIGMA
        $validator = Validator::make($request->all(), [
            'negara_tujuan'  => 'required|in:United States,Malaysia',
            'mata_uang'      => 'required|in:USD,MYR',
            'nomor_rekening' => 'required',
            'nominal_valas'  => 'required|numeric|min:1', // Jumlah mata uang asing yang ingin diterima penerima (e.g., 20 USD)
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

        // 3. CEK REKENING VALAS TUJUAN DI DATABASE
        $valasTarget = ValasAccount::where('negara_tujuan', $request->negara_tujuan)
                                   ->where('mata_uang', $request->mata_uang)
                                   ->where('nomor_rekening', $request->nomor_rekening)
                                   ->first();

        if (!$valasTarget) {
            return response()->json(['status' => false, 'message' => 'Rekening internasional tidak terdaftar'], 404);
        }

        // 4. SIMULASI SKEMA KURS & BIAYA SESUAI FIGMA KAMU
        // Kita kunci kurs statis untuk keperluan demo tugas kuliah
        $rate = ($request->mata_uang === 'USD') ? 17096 : 3850; 
        $hasilKonversiIDR = $request->nominal_valas * $rate;

        // Komponen Biaya tambahan dari screenshot Figma Internasional kamu
        $biayaKomisi = 15000;
        $biayaTransaksi = 35000;
        $totalBiayaIDR = $hasilKonversiIDR + $biayaKomisi + $biayaTransaksi;

        // 5. CEK SALDO RUPIAH USER (Karena bayarnya pakai rupiah)
        $saldoIDR = Saldo::where('ID_User', $userId)->where('mata_uang', 'IDR')->first();
        if (!$saldoIDR || $saldoIDR->jumlah_saldo < $totalBiayaIDR) {
            return response()->json(['status' => false, 'message' => 'Saldo Rupiah Anda tidak cukup untuk membayar total biaya transfer internasional'], 400);
        }

        // 6. EKSEKUSI MUTASI DANA POTONG IDR -> TAMBAH VALAS
        $saldoIDR->jumlah_saldo -= $totalBiayaIDR;
        $saldoIDR->save();

        $valasTarget->saldo_valas += $request->nominal_valas; // Rekening luar negeri menerima mata uang asing utuh
        $valasTarget->save();

        // 7. SIMPAN TRANSAKSI MASTER (Dicatat dalam nominal rupiah dasar)
        $transaksi = Transaksi::create([
            'ID_User'           => $userId,
            'ID_Admin'          => null,
            'jenis_transaksi'   => 'Send-Internasional',
            'nominal'           => $hasilKonversiIDR, 
            'mata_uang'         => 'IDR',
            'status_transaksi'  => 'disetujui',
            'tanggal_transaksi' => now()
        ]);

        // 8. SIMPAN DETAIL TRANSAKSI
        DetailTransaksi::create([
            'ID_Transaksi'   => $transaksi->ID_Transaksi,
            'bank_tujuan'    => 'ACH/CrossBorder',
            'ewallet_tujuan' => null,
            'nama_penerima'  => $valasTarget->nama_penerima,
            'negara_tujuan'  => $request->negara_tujuan
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Transfer internasional berhasil dilakukan',
            'receipt' => [
                'tanggal_waktu'     => $transaksi->created_at->translatedFormat('d F Y, H:i:s'),
                'jenis_layanan'     => 'Send-Internasional',
                'negara_tujuan'     => $request->negara_tujuan,
                'nominal_diterima'  => $request->nominal_valas . ' ' . $request->mata_uang,
                'kurs_berlaku'      => '1 ' . $request->mata_uang . ' = IDR ' . number_format($rate, 0, ',', '.'),
                'hasil_konversi'    => 'IDR ' . number_format($hasilKonversiIDR, 0, ',', '.'),
                'biaya_komisi'      => 'IDR ' . number_format($biayaKomisi, 0, ',', '.'),
                'biaya_transaksi'   => 'IDR ' . number_format($biayaTransaksi, 0, ',', '.'),
                'total_biaya_idr'   => $totalBiayaIDR,
                'sisa_saldo_idr'    => $saldoIDR->jumlah_saldo
            ]
        ], 200);
    }

    public function riwayatTransaksi(Request $request)
    {
        $userId = $request->user()->ID_User;
        $transaksi = Transaksi::with('user')->where('ID_User', $userId)->orderBy('created_at', 'desc')->get();

        if ($transaksi->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'Belum ada riwayat transaksi'], 404);
        }

        return response()->json(['status' => true, 'data' => $transaksi], 200);
    }
}