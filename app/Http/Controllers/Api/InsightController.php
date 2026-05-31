<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class InsightController extends Controller
{
    public function getInsightData(Request $request)
    {
        $userId = Auth::id();
        $tipe = $request->query('tipe', 'pemasukan'); 

        if ($tipe === 'pemasukan') {
            $kategoriKueri = ['pemasukan', 'topup', 'add'];
        } else {
            $kategoriKueri = ['pengeluaran', 'transfer', 'send', 'exchange'];
        }

        // SINKRONISASI: Menyaring data transaksi yang berstatus 'success'
        $transaksiMingguIni = Transaksi::where('ID_User', $userId)
            ->whereIn('jenis_transaksi', $kategoriKueri)
            ->where('status_transaksi', 'success') 
            ->mingguIni()
            ->get();

        $dataChartFormat = [
            'Mon' => 0,
            'Tue' => 0,
            'Wed' => 0, 
            'Thu' => 0,
            'Fri' => 0,
            'Sat' => 0,
            'Sun' => 0,
        ];

        $totalNominalSeminggu = 0;

        foreach ($transaksiMingguIni as $trx) {
            $hariTrx = Carbon::parse($trx->tanggal_transaksi)->format('D'); 

            if ($hariTrx === 'Wed') {
                $hariTrx = 'Wes';
            }

            if (array_key_exists($hariTrx, $dataChartFormat)) {
                $dataChartFormat[$hariTrx] += $trx->nominal;
                $totalNominalSeminggu += $trx->nominal;
            }
        }

        $labels = array_keys($dataChartFormat);
        $values = array_values($dataChartFormat);

        return response()->json([
            'status' => true,
            'message' => 'Data insight ' . $tipe . ' berhasil dikompilasi.',
            'summary' => [
                'tipe_insight' => $tipe,
                'total_akumulasi' => $totalNominalSeminggu,
                'formatted_total' => 'Rp ' . number_format($totalNominalSeminggu, 0, ',', '.')
            ],
            'chart_data' => [
                'labels' => $labels,
                'datasets' => $values
            ]
        ], 200);
    }
}