<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BankAccount;

class BankAccountSeeder extends Seeder
{
    public function run(): void
    {
        $bankData = [
            // BANK MANDIRI
            ['nama_bank' => 'Mandiri', 'nomor_rekening' => '1440235123879', 'nama_pemilik' => 'Putri Santoso', 'saldo' => 25000000],
            ['nama_bank' => 'Mandiri', 'nomor_rekening' => '1440012376874', 'nama_pemilik' => 'Hendra Kurniawan', 'saldo' => 8700000],
            
            // BCA
            ['nama_bank' => 'BCA', 'nomor_rekening' => '0987657654', 'nama_pemilik' => 'Siti Rahmawati', 'saldo' => 10000000],
            ['nama_bank' => 'BCA', 'nomor_rekening' => '0987654322', 'nama_pemilik' => 'Kevin Sanjaya', 'saldo' => 45000000],
            
            // BRI
            ['nama_bank' => 'BRI', 'nomor_rekening' => '528001027055321', 'nama_pemilik' => 'Agus Supriatna', 'saldo' => 1500000],
            ['nama_bank' => 'BRI', 'nomor_rekening' => '324101025671543', 'nama_pemilik' => 'Sri Wahyuni', 'saldo' => 12350000],
        ];

        foreach ($bankData as $data) {
            BankAccount::create($data);
        }
    }
}