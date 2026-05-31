<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Pin;
use App\Models\Saldo;
use App\Models\Admin; // Model Admin sudah di-import di sini
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema; // Mengamankan pemanggilan fungsionalitas Schema

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. BUAT NASABAH UTAMA (Sesuai target UI Dashboard kamu)
        $nasabah = User::create([
            'username' => 'Angeliqia V G Pardosi',
            'nomor_hp' => '081362267690',
            'password' => Hash::make('Nasabah123!'),
            'role'     => 'user'
        ]);

        // Buat PIN untuk Nasabah
        Pin::create([
            'ID_User'  => $nasabah->ID_User,
            'Kode_PIN' => bcrypt('123456')
        ]);

        // FIX: Modal awal data Seeder otomatis Rp 10.000.000
        Saldo::create([
            'ID_User'      => $nasabah->ID_User,
            'jumlah_saldo' => 10000000, 
            'mata_uang'    => 'IDR'
        ]);


        // 2. BUAT AKUN ADMIN DI TABEL 'users' (Untuk keperluan login & token Sanctum)
        User::create([
            'username' => 'Admin TrustPay',
            'nomor_hp' => '081122334455',
            'password' => bcrypt('AdminStrict2026!'),
            'role'     => 'admin'
        ]);


        // 3. FIX WARNING & AMBIGU: Eksekusi langsung tanpa fungsi 'class_exists' yang bertele-tele
        $adminModel = new Admin();
        $adminModel->ID_Admin = 1;
        
        // Pengecekan kolom dibersihkan dari backslash global (\) yang memicu warning VS Code
        if (Schema::hasColumn('admin', 'nama_admin')) {
            $adminModel->nama_admin = 'Super Admin TrustPay';
        } else {
            $adminModel->username = 'Admin TrustPay';
        }
        
        $adminModel->nomor_hp = '081122334455';
        $adminModel->password = bcrypt('AdminStrict2026!');
        $adminModel->save();
    }
}