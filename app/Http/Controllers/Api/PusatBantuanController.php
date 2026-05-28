<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PusatBantuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PusatBantuanController extends Controller
{
    // ==================== SISI USER/NASABAH ====================

    /**
     * 1. Get List FAQ untuk ditampilkan di halaman utama Pusat Bantuan
     */
    public function getFaq()
    {
        $faq = PusatBantuan::where('tipe', 'faq')->get(['ID_Bantuan', 'pertanyaan_atau_subjek', 'jawaban_atau_pesan']);
        
        return response()->json([
            'status' => true,
            'message' => 'Daftar FAQ berhasil dimuat',
            'data' => $faq
        ], 200);
    }

    /**
     * 2. Nasabah mengirim pesan keluhan melalui kotak input teks di bawah
     */
    public function kirimKeluhan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pesan' => 'required|string|min:5'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()], 422);
        }

        $keluhan = PusatBantuan::create([
            'ID_User' => Auth::id(),
            'tipe' => 'keluhan',
            'pertanyaan_atau_subjek' => 'Keluhan Nasabah - ' . Auth::user()->name,
            'jawaban_atau_pesan' => $request->pesan,
            'status_keluhan' => 'open'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Keluhan Anda berhasil dikirim. Tim TrustPay akan segera meninjau.',
            'data' => $keluhan
        ], 201);
    }

    // ==================== SISI ADMIN (Sesuai Disclaimer Anda) ====================

    /**
     * 3. Admin menambahkan FAQ baru
     */
    public function tambahFaq(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pertanyaan' => 'required|string',
            'jawaban'    => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()], 422);
        }

        $faq = PusatBantuan::create([
            'tipe' => 'faq',
            'pertanyaan_atau_subjek' => $request->pertanyaan,
            'jawaban_atau_pesan' => $request->jawaban
        ]);

        return response()->json([
            'status' => true,
            'message' => 'FAQ baru berhasil ditambahkan oleh Admin!',
            'data' => $faq
        ], 201);
    }

    /**
     * 4. Admin mengedit FAQ yang sudah ada
     */
    public function editFaq(Request $request, $id)
    {
        $faq = PusatBantuan::where('tipe', 'faq')->find($id);

        if (!$faq) {
            return response()->json(['status' => false, 'message' => 'Data FAQ tidak ditemukan'], 404);
        }

        $faq->update([
            'pertanyaan_atau_subjek' => $request->pertanyaan ?? $faq->pertanyaan_atau_subjek,
            'jawaban_atau_pesan' => $request->jawaban ?? $faq->jawaban_atau_pesan
        ]);

        return response()->json([
            'status' => true,
            'message' => 'FAQ berhasil diperbarui oleh Admin!',
            'data' => $faq
        ], 200);
    }

    /**
     * 5. Admin menghapus FAQ
     */
    public function hapusFaq($id)
    {
        $faq = PusatBantuan::where('tipe', 'faq')->find($id);

        if (!$faq) {
            return response()->json(['status' => false, 'message' => 'Data FAQ tidak ditemukan'], 404);
        }

        $faq->delete();

        return response()->json([
            'status' => true,
            'message' => 'FAQ berhasil dihapus dari sistem.'
        ], 200);
    }
}