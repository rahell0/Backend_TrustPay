<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PusatBantuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PusatBantuanController extends Controller
{
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
            'pertanyaan_atau_subjek' => 'Keluhan Nasabah - ' . Auth::user()->username, // Diperbaiki dari 'name' ke 'username'
            'jawaban_atau_pesan' => $request->pesan,
            'status_keluhan' => 'Baru' // Sinkron dengan Admin UI: 'Baru', 'Proses', 'Selesai'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Keluhan Anda berhasil dikirim.',
            'data' => $keluhan
        ], 201);
    }

    public function ubahStatusKeluhan(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['status' => false, 'message' => 'Akses khusus admin ditolak'], 403);
        }

        $request->validate(['status' => 'required|in:Baru,Proses,Selesai']);
        
        $keluhan = PusatBantuan::where('tipe', 'keluhan')->find($id);
        if (!$keluhan) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        $keluhan->update(['status_keluhan' => $request->status]);

        return response()->json([
            'status' => true,
            'message' => 'Status keluhan berhasil dirubah menjadi ' . $request->status
        ], 200);
    }

    public function getKeluhanAdmin()
    {
        $keluhan = PusatBantuan::where('tipe', 'keluhan')->orderBy('created_at', 'desc')->get();
        return response()->json(['status' => true, 'data' => $keluhan], 200);
    }
}