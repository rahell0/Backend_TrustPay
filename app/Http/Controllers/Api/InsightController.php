<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class InsightController extends Controller
{
    /**
     * Mengambil data akumulasi mingguan untuk diplot ke Chart UI TrustPay
     */
    public function getInsightData(Request $request)
    {
        // 1. Ambil ID User yang sedang login (Fitur personal nasabah)
        $userId = Auth::id();

        // 2. Tentukan tipe insight dari request user ('pemasukan' atau 'pengeluaran')
        // Default diset ke 'pemasukan' jika user baru pertama kali meload halaman
        $tipe = $request->query('tipe', 'pemasukan'); 

        // Pemetaan kategori transaksi berdasarkan jenis riil di aplikasi
        // Sesuai UI Figma: ADD (Top up Shoopepay dll) masuk pemasukan/pengeluaran e-wallet
        if ($tipe === 'pemasukan') {
            $kategoriKueri = ['pemasukan', 'topup', 'add'];
        } else {
            $kategoriKueri = ['pengeluaran', 'transfer', 'send', 'exchange'];
        }

        // 3. Ambil data transaksi user selama MINGGU INI menggunakan Scope yang dibuat tadi
        $transaksiMingguIni = Transaksi::where('ID_User', $userId)
            ->whereIn('jenis_transaksi', $kategoriKueri)
            ->where('status_transaksi', 'sukses') // Hanya transaksi berhasil yang masuk hitungan grafik
            ->mingguIni()
            ->get();

        // 4. Struktur data default untuk sumbu X Chart (Senin - Minggu seperti di Figma kamu)
        // Kita inisialisasi awal nilai Rp 0 untuk semua hari agar grafik tidak kosong/patah
        $dataChartFormat = [
            'Mon' => 0,
            'Tue' => 0,
            'Wes' => 0, // Disesuaikan dengan teks UI figma kamu yang tertulis 'Wes'
            'Thu' => 0,
            'Fri' => 0,
            'Sat' => 0,
            'Sun' => 0,
        ];

        // Variabel pembantu untuk menghitung total akumulasi dalam satu minggu penuh
        $totalNominalSeminggu = 0;

        // 5. Melakukan Looping data transaksi dan mengelompokkannya berdasarkan hari
        foreach ($transaksiMingguIni as $trx) {
            // Ubah string tanggal_transaksi menjadi objek Carbon untuk mendeteksi hari
            $hariTrx = Carbon::parse($trx->tanggal_transaksi)->format('D'); // Menghasilkan: Mon, Tue, Wed, dll.

            // Penyesuaian penulisan hari Rabu sesuai keinginan teks UI Figma ('Wes')
            if ($hariTrx === 'Wed') {
                $hariTrx = 'Wes';
            }

            // Jika hari tersebut terdaftar di format chart kita, akumulasikan nominal rupiahnya
            if (array_key_exists($hariTrx, $dataChartFormat)) {
                $dataChartFormat[$hariTrx] += $trx->nominal;
                $totalNominalSeminggu += $trx->nominal;
            }
        }

        // 6. Siapkan struktur array terpisah untuk label (Sumbu X) dan data angka (Sumbu Y)
        // Pemisahan ini merupakan format standar yang sangat disukai oleh frontend developer untuk chart
        $labels = array_keys($dataChartFormat);
        $values = array_values($dataChartFormat);

        // 7. Kembalikan response JSON yang rapi untuk di-render oleh Frontend
        return response()->json([
            'status' => true,
            'message' => 'Data insight ' . $tipe . ' berhasil dikompilasi.',
            'summary' => [
                'tipe_insight' => $tipe,
                'total_akumulasi' => $totalNominalSeminggu,
                'formatted_total' => 'Rp ' . number_format($totalNominalSeminggu, 0, ',', '.')
            ],
            'chart_data' => [
                'labels' => $labels, // ['Mon', 'Tue', 'Wes', 'Thu', 'Fri', 'Sat', 'Sun']
                'datasets' => $values // [150000, 200000, 120000, 40000, ...]
            ]
        ], 200);
    }
}