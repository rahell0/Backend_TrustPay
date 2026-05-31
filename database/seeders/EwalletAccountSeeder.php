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
        // 1. DATA SHOPEEPAY
        $shopeepayData = [
            ['nomor_telepon' => '081362267690', 'nama_pemilik' => 'Rahel Jessica', 'saldo' => 500000],
            ['nomor_telepon' => '081234567891', 'nama_pemilik' => 'Andi Wijaya', 'saldo' => 1250000],
            ['nomor_telepon' => '081987654321', 'nama_pemilik' => 'Rina Permata', 'saldo' => 75000],
        ];
        foreach ($shopeepayData as $data) {
            ShopeepayAccount::create($data);
        }

        // 2. DATA GOPAY
        $gopayData = [
            ['nomor_telepon' => '089987654321', 'nama_pemilik' => 'Siti Aminah', 'saldo' => 150000],
            ['nomor_telepon' => '087711223344', 'nama_pemilik' => 'Bambang Pamungkas', 'saldo' => 3200000],
            ['nomor_telepon' => '085299887766', 'nama_pemilik' => 'Dewi Lestari', 'saldo' => 450000],
        ];
        foreach ($gopayData as $data) {
            GopayAccount::create($data);
        }

        // 3. DATA DANA
        $danaData = [
            ['nomor_telepon' => '081122334455', 'nama_pemilik' => 'Ahmad Dhani', 'saldo' => 75000],
            ['nomor_telepon' => '081399881122', 'nama_pemilik' => 'Eko Prasetyo', 'saldo' => 600000],
            ['nomor_telepon' => '082155667788', 'nama_pemilik' => 'Siska Amelia', 'saldo' => 15000],
        ];
        foreach ($danaData as $data) {
            DanaAccount::create($data);
        }
    }
}