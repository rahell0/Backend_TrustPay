<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PusatBantuan;

class PusatBantuanSeeder extends Seeder
{
    public function run(): void
    {
        PusatBantuan::create([
            'tipe' => 'faq',
            'pertanyaan_atau_subjek' => 'Tentang TrustPay.id',
            'jawaban_atau_pesan' => 'TrustPay.id adalah platform dompet digital terpercaya untuk kebutuhan transaksi nasional, internasional, dan penukaran valas.'
        ]);

        PusatBantuan::create([
            'tipe' => 'faq',
            'pertanyaan_atau_subjek' => 'Pemberitahuan Privasi',
            'jawaban_atau_pesan' => 'Kami berkomitmen penuh menjaga kerahasiaan data pribadi serta keamanan saldo seluruh pengguna TrustPay.'
        ]);

        PusatBantuan::create([
            'tipe' => 'faq',
            'pertanyaan_atau_subjek' => 'Syarat dan Ketentuan',
            'jawaban_atau_pesan' => 'Pengguna wajib berusia minimal 17 tahun dan mematuhi regulasi keuangan yang berlaku di Indonesia.'
        ]);
    }
}