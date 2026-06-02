<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KursSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('kurs')->truncate();

        DB::table('kurs')->insert([
            [
                'kode_valas' => 'MYR', 
                'nama_valas' => 'Malaysian Ringgit', 
                'kurs_beli' => 4450.00, 
                'kurs_jual' => 4540.00, 
                'nilai_ke_idr' => 4496.00, 
                'created_at' => now(), 
                'updated_at' => now()
            ],
            [
                'kode_valas' => 'SGD', 
                'nama_valas' => 'Singapore Dollar', 
                'kurs_beli' => 13850.00, 
                'kurs_jual' => 14010.00, 
                'nilai_ke_idr' => 13930.00, 
                'created_at' => now(), 
                'updated_at' => now()
            ],
            [
                'kode_valas' => 'USD', 
                'nama_valas' => 'United States Dollar', 
                'kurs_beli' => 17750.00, 
                'kurs_jual' => 17910.00, 
                'nilai_ke_idr' => 17828.00, 
                'created_at' => now(), 
                'updated_at' => now()
            ],
            [
                'kode_valas' => 'IDR', 
                'nama_valas' => 'Indonesian Rupiah', 
                'kurs_beli' => 1.00, 
                'kurs_jual' => 1.00, 
                'nilai_ke_idr' => 1.00, 
                'created_at' => now(), 
                'updated_at' => now()
            ],
        ]);
    }
}