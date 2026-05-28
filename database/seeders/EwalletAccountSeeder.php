<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShopeepayAccount;
use App\Models\GopayAccount;
use App\Models\DanaAccount;

class EwalletAccountSeeder extends Seeder
{
    public function run(): void
    {
        ShopeepayAccount::create([
            'nomor_telepon' => '081362267690',
            'nama_pemilik'  => 'Rahel Jessica',
            'saldo'         => 500000
        ]);

        GopayAccount::create([
            'nomor_telepon' => '089987654321',
            'nama_pemilik'  => 'Siti Aminah',
            'saldo'         => 150000
        ]);

        DanaAccount::create([
            'nomor_telepon' => '081122334455',
            'nama_pemilik'  => 'Ahmad Dhani',
            'saldo'         => 75000
        ]);
    }
}