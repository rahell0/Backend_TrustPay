<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Pin;
use App\Models\Saldo;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Bersihkan data lama dengan aman agar tidak terjadi bentrokan duplikat unique key nomor_hp
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        Pin::truncate();
        Saldo::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. DATA NASABAH UTAMA (Aktor Utama untuk Demo UI Dashboard Nasabah)
        $nasabahUtama = User::create([
            'username' => 'Angeliqia V G Pardosi',
            'nomor_hp' => '081362267690',
            'password' => Hash::make('Nasabah123@'),
            'role'     => 'user',
            'created_at' => '2026-01-05 08:30:22'
        ]);

        Pin::create([
            'ID_User'  => $nasabahUtama->ID_User,
            'Kode_PIN' => Hash::make('123456')
        ]);

        Saldo::create([
            'ID_User'      => $nasabahUtama->ID_User,
            'jumlah_saldo' => 10000000, // Rp 10.000.000 saldo awal simulasi sesuai Figma
            'mata_uang'    => 'IDR'
        ]);

        // 2. 20 DATA DUMMY NASABAH TAMBAHAN (Disesuaikan Murni untuk Mengisi Tabel Frontend Admin)
        $dataNasabahDummy = [
            ['username' => 'Ahmad Syarif', 'nomor_hp' => '085211223344', 'saldo' => 4500000, 'tgl' => '2026-01-12 14:20:11'],
            ['username' => 'Clara Angelica', 'nomor_hp' => '087855667788', 'saldo' => 12500000, 'tgl' => '2026-02-05 09:11:45'],
            ['username' => 'Budi Darmawan', 'nomor_hp' => '081399001122', 'saldo' => 150000, 'tgl' => '2026-02-18 16:45:00'],
            ['username' => 'Rian Hidayat', 'nomor_hp' => '082188776655', 'saldo' => 7800000, 'tgl' => '2026-02-25 11:02:30'],
            ['username' => 'Siti Aminah', 'nomor_hp' => '085733445566', 'saldo' => 2350000, 'tgl' => '2026-03-02 13:50:12'],
            ['username' => 'Dedi Wijaya', 'nomor_hp' => '081122334455', 'saldo' => 6200000, 'tgl' => '2026-03-10 10:00:00'],
            ['username' => 'Putri Santoso', 'nomor_hp' => '089877665544', 'saldo' => 5400000, 'tgl' => '2026-03-15 15:30:22'],
            ['username' => 'Hendra Setiawan', 'nomor_hp' => '081255443322', 'saldo' => 19000000, 'tgl' => '2026-03-22 07:45:10'],
            ['username' => 'Farhan Saputra', 'nomor_hp' => '081344556677', 'saldo' => 850000, 'tgl' => '2026-04-01 11:15:34'],
            ['username' => 'Dewi Lestari', 'nomor_hp' => '085677889900', 'saldo' => 3400000, 'tgl' => '2026-04-04 14:22:19'],
            ['username' => 'Kevin Sanjaya', 'nomor_hp' => '087711223344', 'saldo' => 27500000, 'tgl' => '2026-04-12 09:05:00'],
            ['username' => 'Anisa Rahmawati', 'nomor_hp' => '081288990011', 'saldo' => 1200000, 'tgl' => '2026-04-18 16:40:12'],
            ['username' => 'Rizky Febrian', 'nomor_hp' => '082233445566', 'saldo' => 9300000, 'tgl' => '2026-04-26 10:55:23'],
            ['username' => 'Mega Utami', 'nomor_hp' => '081955667788', 'saldo' => 4100000, 'tgl' => '2026-05-02 13:14:02'],
            ['username' => 'Aditya Pratama', 'nomor_hp' => '085344332211', 'saldo' => 15500000, 'tgl' => '2026-05-08 08:20:50'],
            ['username' => 'Sinta Bella', 'nomor_hp' => '081199887766', 'saldo' => 300000, 'tgl' => '2026-05-14 11:30:15'],
            ['username' => 'Ferry Irawan', 'nomor_hp' => '081266778899', 'saldo' => 8200000, 'tgl' => '2026-05-20 15:45:44'],
            ['username' => 'Yulia Ningsih', 'nomor_hp' => '085711229988', 'saldo' => 5100000, 'tgl' => '2026-05-24 09:12:01'],
            ['username' => 'Doni Salman', 'nomor_hp' => '082144551122', 'saldo' => 32000000, 'tgl' => '2026-05-28 14:02:10'],
            ['username' => 'Rina Nose', 'nomor_hp' => '081388772211', 'saldo' => 2150000, 'tgl' => '2026-05-31 17:25:36']
        ];

        foreach ($dataNasabahDummy as $dummy) {
            // Buat entitas User baru
            $userDummy = User::create([
                'username' => $dummy['username'],
                'nomor_hp' => $dummy['nomor_hp'],
                'password' => Hash::make('Nasabah123@'),
                'role'     => 'user',
                'created_at' => $dummy['tgl'],
                'updated_at' => now()
            ]);

            // Daftarkan PIN default untuk seluruh akun dummy
            Pin::create([
                'ID_User'  => $userDummy->ID_User,
                'Kode_PIN' => Hash::make('123456')
            ]);

            // Alokasikan jumlah saldo simpanan IDR masing-masing user dummy
            Saldo::create([
                'ID_User'      => $userDummy->ID_User,
                'jumlah_saldo' => $dummy['saldo'],
                'mata_uang'    => 'IDR'
            ]);
        }
    }
}