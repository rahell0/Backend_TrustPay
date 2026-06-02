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
        // Hubungkan riwayat transaksi ke akun visual Angeliqia
        $user = User::where('username', 'Angeliqia V G Pardosi')->first();

        if ($user) {
            // 1. Riwayat Top Up E-Wallet (Pemasukan / Potongan Diambil dari Saldo)
            $trx1 = Transaksi::create([
                'ID_User'           => $user->ID_User,
                'jenis_transaksi'   => 'Top Up E-Wallet',
                'nominal'           => 150000,
                'biaya_admin'       => 0, // Sesuai kesepakatan UI Topup gratis adm
                'mata_uang'         => 'IDR',
                'status_transaksi'  => 'success',
                'tanggal_transaksi' => now()->subDays(1)->format('Y-m-d H:i:s')
            ]);
            
            DetailTransaksi::create([
                'ID_Transaksi'   => $trx1->ID_Transaksi,
                'ewallet_tujuan' => 'ShopeePay',
                'nomor_tujuan'   => '081262267690',
                'nama_penerima'  => 'Angeliqia V.G Pardosi'
            ]);

            // 2. Riwayat Transfer Nasional ke Rekening Mandiri
            $trx2 = Transaksi::create([
                'ID_User'           => $user->ID_User,
                'jenis_transaksi'   => 'Transfer Nasional',
                'nominal'           => 500000,
                'biaya_admin'       => 6500, // Biaya standar transfer antar bank nasional di UI
                'mata_uang'         => 'IDR',
                'status_transaksi'  => 'success',
                'tanggal_transaksi' => now()->format('Y-m-d H:i:s')
            ]);
            
            DetailTransaksi::create([
                'ID_Transaksi' => $trx2->ID_Transaksi,
                'bank_tujuan'  => 'Mandiri',
                'nomor_tujuan' => '1440235123879',
                'nama_penerima'=> 'Putri Santoso'
            ]);
        }
    }
}