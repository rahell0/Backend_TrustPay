<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KursSeeder extends Seeder
{
    public function run(): void
    {
       DB::table('kurs')->insert([
    [
        'kode_valas' => 'MYR', 
        'nama_valas' => 'Malaysian Ringgit', 
        'nilai_ke_idr' => 3600.00, 
        'created_at' => now(), 
        'updated_at' => now()
    ],
    [
        'kode_valas' => 'SGD', 
        'nama_valas' => 'Singapore Dollar', 
        'nilai_ke_idr' => 12650.00, // Nilai asumsi kurs SGD ke IDR, silakan disesuaikan jika ada angka pasti dari modul kuliahmu
        'created_at' => now(), 
        'updated_at' => now()
    ],
    [
        'kode_valas' => 'USD', 
        'nama_valas' => 'United States Dollar', 
        'nilai_ke_idr' => 17096.00, 
        'created_at' => now(), 
        'updated_at' => now()
    ]
    ]);
    }
}