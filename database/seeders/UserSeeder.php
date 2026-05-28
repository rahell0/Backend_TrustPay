<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Pin;
use App\Models\Saldo;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Nasabah Utama (Sesuai target UI Dashboard kamu)
        $nasabah = User::create([
            'username' => 'Angeliqia V G Pardosi',
            'nomor_hp' => '081362267690',
            'password' => Hash::make('Nasabah123!'),
            'role'     => 'user'
        ]);

        Pin::create([
            'ID_User'  => $nasabah->ID_User,
            'Kode_PIN' => Hash::make('123456')
        ]);

        Saldo::create([
            'ID_User'      => $nasabah->ID_User,
            'jumlah_saldo' => 7500000, // Modal awal Dashboard Rp 7.500.000
            'mata_uang'    => 'IDR'
        ]);

        // Akun Admin
        User::create([
            'username' => 'Super Admin TrustPay',
            'nomor_hp' => '081122334455',
            'password' => Hash::make('AdminStrict2026!'),
            'role'     => 'admin'
        ]);
    }
}