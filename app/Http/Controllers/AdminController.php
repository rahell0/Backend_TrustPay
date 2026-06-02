<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Get Dashboard Stats
     */
    public function getDashboardStats()
    {
        return response()->json([
            'status' => true,
            'data' => [
                'total_saldo_tersimpan' => '100.000.000,00 IDR', // Simulasi statis sesuai kebutuhan UI Frontend
                'grafik_mingguan' => [10, 20, 15, 30, 25, 40, 35],
                'log_aktivitas' => [
                    ['waktu' => now()->toDateTimeString(), 'pesan' => 'Sistem backend siap digunakan.']
                ]
            ]
        ]);
    }

    /**
     * Get User Management (Fungsi yang tadi tersangkut di seeder)
     */
    public function getUserManagement()
    {
        // Mengambil semua user dengan role 'user' (Nasabah)
        $users = DB::table('users')->where('role', 'user')->get();

        $formattedUsers = $users->map(function ($user) {
            // Simulasi status khusus untuk Budi Darmawan sesuai logika UI Anda
            $statusSimulasi = ($user->username === 'Budi Darmawan') ? 'Butuh Verifikasi' : 'Aktif';
            
            // Generate email simulasi dari nama
            $emailSimulasi = strtolower(str_replace(' ', '', $user->username)) . '@trustpay.com';

            return [
                'id' => $user->id,
                'nama' => $user->username,
                'email' => $emailSimulasi,
                'tanggal_bergabung' => date('d M Y', strtotime($user->created_at)),
                'status' => $statusSimulasi,
                'no_telepon' => $user->nomor_hp
            ];
        });

        return response()->json([
            'status' => true,
            'summary' => [
                'total_nasabah' => $users->count(),
                'nasabah_aktif' => $users->count() // Menyesuaikan jumlah total
            ],
            'users' => $formattedUsers
        ]);
    }

    /**
     * Get Global Reports
     */
    public function getGlobalReports()
    {
        return response()->json([
            'status' => true,
            'reports' => [
                [
                    'id_transaksi' => 'TX-9901',
                    'jenis' => 'Top Up',
                    'jumlah' => '5.000.000,00 IDR',
                    'status' => 'Sukses',
                    'waktu' => now()->subMinutes(30)->toDateTimeString()
                ]
            ]
        ]);
    }
}