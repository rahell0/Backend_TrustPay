<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,            // Membuat Admin & Nasabah
            KursSeeder::class,            // Membuat Data Kurs Tukar
            EwalletAccountSeeder::class,  // Simulator E-Wallet External
            BankAccountSeeder::class,     // Simulator Rekening Nasional External
            ValasAccountSeeder::class,    // Simulator Rekening Valas International External
            TransaksiHistorySeeder::class,// Riwayat Transaksi Awal Nasabah Utama
            PusatBantuanSeeder::class,    // FAQ & Antrean Keluhan Sesuai UI Admin figma
        ]);
    }
}