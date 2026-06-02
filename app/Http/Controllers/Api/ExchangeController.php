<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ExchangeController extends Controller
{
    /**
     * Mengambil daftar kurs untuk ditampilkan di UI User & Admin
     */
    public function index()
    {
        $kurs = DB::table('kurs')->get();
        return response()->json([
            'status' => true,
            'message' => 'Data kurs berhasil dimuat.',
            'data' => $kurs
        ], 200);
    }

    /**
     * Fungsi Kritis: Admin memperbarui nilai kurs jual & beli dari Frontend Admin
     */
    public function updateKurs(Request $request)
    {
        // Gembok Keamanan Tingkat Controller
        if ($request->user()->role !== 'admin') {
            return response()->json(['status' => false, 'message' => 'Akses ditolak. Anda bukan admin.'], 403);
        }

        // Validasi input parameter dari Frontend Admin temanmu
        $validator = Validator::make($request->all(), [
            'kode_valas' => 'required|string|exists:kurs,kode_valas',
            'kurs_beli'  => 'required|numeric|min:0',
            'kurs_jual'  => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Hitung nilai tengah secara otomatis untuk keperluan rumus kalkulator backup
        $nilaiTengah = ($request->kurs_beli + $request->kurs_jual) / 2;

        // Eksekusi update data ke database
        DB::table('kurs')
            ->where('kode_valas', $request->kode_valas)
            ->update([
                'kurs_beli'    => $request->kurs_beli,
                'kurs_jual'    => $request->kurs_jual,
                'nilai_ke_idr' => $nilaiTengah,
                'updated_at'   => now()
            ]);

        return response()->json([
            'status' => true,
            'message' => 'Kurs mata uang ' . $request->kode_valas . ' berhasil diperbarui oleh Admin.'
        ], 200);
    }
}