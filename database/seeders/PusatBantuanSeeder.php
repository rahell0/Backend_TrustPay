<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PusatBantuan;
use App\Models\User;

class PusatBantuanSeeder extends Seeder
{
    public function run(): void
    {
        // 1. DATA FAQ UMUM NASABAH
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

        PusatBantuan::create([
            'tipe' => 'faq',
            'pertanyaan_atau_subjek' => 'Penutupan akun TrustPay.id',
            'jawaban_atau_pesan' => 'Untuk melakukan penutupan akun, pastikan seluruh saldo Anda sudah ditarik dan hubungi layanan pelanggan melalui menu kontak bantuan.'
        ]);

        // =========================================================================
        // 2. DATA ADUAN MASUK SAKTI (Sesuai Visual Dashboard Admin UI: Clara Angelica)
        // =========================================================================
        $clara = User::where('username', 'Clara Angelica')->first();

        if ($clara) {
            PusatBantuan::create([
                'ID_User' => $clara->ID_User,
                'tipe' => 'keluhan',
                'pertanyaan_atau_subjek' => 'Gagal Kirim Penukaran Mandiri', // Persis sesuai string Antrean Aduan UI Admin
                'jawaban_atau_pesan' => 'Mohon bantuan admin, saya melakukan transfer penukaran dana ke bank Mandiri namun statusnya menggantung dan saldo berkurang.',
                'status_keluhan' => 'Baru' // Akan memicu label "Baru" / "Proses" di UI Admin Help Center
            ]);
        }
    }
}