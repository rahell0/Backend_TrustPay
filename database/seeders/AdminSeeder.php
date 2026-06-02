<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Bersihkan tabel sebelum insert agar tidak terjadi Duplicate Entry
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('users')->truncate();
        DB::table('saldos')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ==========================================
        // 1. DATA DATABASE ADMIN (Sesuai Spek Frontend)
        // ==========================================
        DB::table('users')->insert([
            'username' => 'Admin TrustPay',
            'nomor_hp' => '081234567890',
            'role' => 'admin',
            'password' => Hash::make('AdminStrictT2026!'), // Password Khusus Admin
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        // Catatan: Admin tidak ditambahkan ke tabel saldos karena Admin tidak punya saldo.

        // ==========================================
        // 2. DATA DATABASE USER / NASABAH SIMULASI
        // ==========================================
        DB::table('users')->insert([
            [
                'username' => 'Ahmad Syarif',
                'nomor_hp' => '082111222333',
                'role' => 'user',
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'Budi Darmawan',
                'nomor_hp' => '085777888999',
                'role' => 'user',
                'password' => Hash::make('password123'),
                'created_at' => now()->subDays(5), // Simulasi bergabung 5 hari lalu untuk UI
                'updated_at' => now(),
            ]
        ]);
    }
}