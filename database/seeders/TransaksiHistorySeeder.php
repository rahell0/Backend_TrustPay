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
        // Mencari nasabah utama untuk dikaitkan dengan riwayat transaksi
        $user = User::where('role', 'user')->first();

        if ($user) {
            // 1. Riwayat Otomatis Top Up E-Wallet (Masuk kategori Pemasukan di Grafik)
            $trx1 = Transaksi::create([
                'ID_User'           => $user->ID_User,
                'jenis_transaksi'   => 'topup',
                'nominal'           => 150000,
                'mata_uang'         => 'IDR',
                'status_transaksi'  => 'success', // SINKRON
                'tanggal_transaksi' => now()->subDays(1)->format('Y-m-d H:i:s')
            ]);
            
            DetailTransaksi::create([
                'ID_Transaksi'   => $trx1->ID_Transaksi,
                'ewallet_tujuan' => 'ShopeePay',
                'nama_penerima'  => 'Angeliqia V.G Pardosi'
            ]);

            // 2. Riwayat Otomatis Transfer Nasional (Masuk kategori Pengeluaran di Grafik)
            $trx2 = Transaksi::create([
                'ID_User'           => $user->ID_User,
                'jenis_transaksi'   => 'transfer',
                'nominal'           => 500000,
                'mata_uang'         => 'IDR',
                'status_transaksi'  => 'success', // SINKRON
                'tanggal_transaksi' => now()->format('Y-m-d H:i:s')
            ]);
            
            DetailTransaksi::create([
                'ID_Transaksi' => $trx2->ID_Transaksi,
                'bank_tujuan'  => 'Bank Mandiri',
                'nama_penerima'=> 'Budi Santoso'
            ]);
        }
    }
}