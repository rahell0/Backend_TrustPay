<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KursSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('kurs')->insert([
            ['kode_valas' => 'USD', 'nama_valas' => 'United States Dollar', 'nilai_ke_idr' => 17096.00, 'created_at' => now(), 'updated_at' => now()],
            ['kode_valas' => 'EUR', 'nama_valas' => 'Euro', 'nilai_ke_idr' => 18500.00, 'created_at' => now(), 'updated_at' => now()],
            ['kode_valas' => 'JPY', 'nama_valas' => 'Japanese Yen', 'nilai_ke_idr' => 110.00, 'created_at' => now(), 'updated_at' => now()],
            ['kode_valas' => 'MYR', 'nama_valas' => 'Malaysian Ringgit', 'nilai_ke_idr' => 3600.00, 'created_at' => now(), 'updated_at' => now()]
        ]);
    }
}