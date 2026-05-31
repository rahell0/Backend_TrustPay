<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ValasAccount; // Pastikan model ValasAccount sudah ada

class ValasAccountSeeder extends Seeder
{
    public function run(): void
    {
        // Menyediakan data rekening internasional (Valas) untuk divalidasi
       
        $ValasData = [
        [
        //AMERIKA SERIKAT (USD)
            'negara_tujuan'  => 'United States',
            'mata_uang'      => 'USD',
            'nomor_rekening' => '9988776655', // Target ketik untuk transfer internasional
            'nama_penerima'  => 'John Doe',
            'routing_number' => '123456789', // ACH Routing Number khas Amerika di Figma kamu
            'saldo_valas'    => 5000 // Saldo dalam USD
        ],
        [
            'negara_tujuan'  => 'United States',
            'mata_uang'      => 'USD',
            'nomor_rekening' => '9988776656',
            'nama_penerima'  => 'Michael Smith',
            'routing_number' => '987654321', 
            'saldo_valas'    => 12500
        ],
        [
            //MALAYSA(RINGGIT)
            'negara_tujuan'  => 'Malaysia',
            'mata_uang'      => 'MYR',
            'nomor_rekening' => '5566778899',
            'nama_penerima'  => 'Mohammad Nazri',
            'routing_number' => null, // Malaysia biasanya tidak pakai ACH routing number
            'saldo_valas'    => 12000 // Saldo dalam MYR
        ],
        [
            //EROPA(EUR)
            'negara_tujuan'  => 'Germany', // Negara bagian Eropa
            'mata_uang'      => 'EUR',
            'nomor_rekening' => 'DE89370400440532013000', // Format rekening Eropa (IBAN)
            'nama_penerima'  => 'Hans Müller',
            'routing_number' => null, 
            'saldo_valas'    => 3200
        ],
        [
            'negara_tujuan'  => 'France', // Negara bagian Eropa
            'mata_uang'      => 'EUR',
            'nomor_rekening' => 'FR76300060000112345678901', 
            'nama_penerima'  => 'Pierre Dubois',
            'routing_number' => null, 
            'saldo_valas'    => 750
        ],
    ];

    foreach ($ValasData as $data) {
        ValasAccount::create($data);

    }
  }
}