<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\User;

class TransaksiHistorySeeder extends Seeder
{
    public function run(): void
    {
        // Cari ID User dari Angeliqia yang dibuat di UserSeeder tadi
        $user = User::where('role', 'user')->first();

        if ($user) {
            // Data Riwayat Top Up E-Wallet
            $trx1 = Transaksi::create([
                'ID_User'           => $user->ID_User,
                'jenis_transaksi'   => 'topup',
                'nominal'           => 150000,
                'mata_uang'         => 'IDR',
                'status_transaksi'  => 'success',
                'tanggal_transaksi' => now()->subDays(2)->format('Y-m-d H:i:s')
            ]);
            DetailTransaksi::create([
                'ID_Transaksi'   => $trx1->ID_Transaksi,
                'ewallet_tujuan' => 'ShopeePay',
                'nama_penerima'  => 'Angeliqia V.G Pardosi'
            ]);

            // Data Riwayat Transfer Nasional
            $trx2 = Transaksi::create([
                'ID_User'           => $user->ID_User,
                'jenis_transaksi'   => 'transfer',
                'nominal'           => 500000,
                'mata_uang'         => 'IDR',
                'status_transaksi'  => 'success',
                'tanggal_transaksi' => now()->subDay()->format('Y-m-d H:i:s')
            ]);
            DetailTransaksi::create([
                'ID_Transaksi' => $trx2->ID_Transaksi,
                'bank_tujuan'  => 'Bank Mandiri',
                'nama_penerima'=> 'Budi Santoso'
            ]);
        }
    }
}