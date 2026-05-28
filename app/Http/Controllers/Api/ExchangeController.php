<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kurs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExchangeController extends Controller
{
    // 1. Mengambil daftar kurs untuk Dropdown di UI Figma
    public function index()
    {
        $daftarKurs = Kurs::all();
        
        return response()->json([
            'status' => true,
            'message' => 'Daftar nilai tukar mata uang berhasil dimuat',
            'data' => $daftarKurs
        ], 200);
    }

    // 2. Logika Kalkulator Penukaran (IDR -> Valas ATAU Valas -> IDR)
    public function hitungKalkulasi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mata_uang_asal'   => 'required|string|max:3',
            'mata_uang_tujuan' => 'required|string|max:3',
            'nominal_tukar'    => 'required|numeric|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 422);
        }

        $asal = strtoupper($request->mata_uang_asal);
        $tujuan = strtoupper($request->mata_uang_tujuan);
        $nominal = $request->nominal_tukar;

        $hasilKonversi = 0;
        $kursBerlakuText = "";

        // Skenario A: Jika konversi dari Rupiah (IDR) ke Mata Uang Asing
        if ($asal === 'IDR') {
            $dataKurs = Kurs::where('kode_valas', $tujuan)->first();
            
            if (!$dataKurs) {
                return response()->json(['status' => false, 'message' => 'Mata uang tujuan tidak didukung'], 404);
            }

            // Rumus: Rupiah dibagi Nilai Kurs Valas
            $hasilKonversi = $nominal / $dataKurs->nilai_ke_idr;
            $kursBerlakuText = "1 " . $tujuan . " = IDR " . number_format($dataKurs->nilai_ke_idr, 0, ',', '.');
        } 
        
        // Skenario B: Jika konversi dari Mata Uang Asing ke Rupiah (IDR)
        elseif ($tujuan === 'IDR') {
            $dataKurs = Kurs::where('kode_valas', $asal)->first();

            if (!$dataKurs) {
                return response()->json(['status' => false, 'message' => 'Mata uang asal tidak didukung'], 404);
            }

            // Rumus: Nominal Valas dikali Nilai Kurs
            $hasilKonversi = $nominal * $dataKurs->nilai_ke_idr;
            $kursBerlakuText = "1 " . $asal . " = IDR " . number_format($dataKurs->nilai_ke_idr, 0, ',', '.');
        } 
        
        // Skenario C: Proteksi jika asal dan tujuan sama (misal IDR ke IDR)
        else {
            if ($asal === $tujuan) {
                $hasilKonversi = $nominal;
                $kursBerlakuText = "1 " . $asal . " = 1 " . $tujuan;
            } else {
                return response()->json(['status' => false, 'message' => 'Fitur ini khusus konversi yang melibatkan mata uang IDR'], 400);
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Kalkulasi berhasil dihitung',
            'kalkulator' => [
                'mata_uang_asal'   => $asal,
                'mata_uang_tujuan' => $tujuan,
                'nominal_asal'     => $nominal,
                'kurs_berlaku'     => $kursBerlakuText,
                'hasil_konversi'   => round($hasilKonversi, 2), // Batasi 2 angka di belakang koma (Sesuai USD 57,53 di Figma)
                'format_tampilan'  => $tujuan . " " . number_format($hasilKonversi, 2, ',', '.')
            ]
        ], 200);
    }
}