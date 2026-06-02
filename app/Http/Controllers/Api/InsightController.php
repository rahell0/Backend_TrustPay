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

        $kategoriKueri = ($tipe === 'pemasukan') 
            ? ['pemasukan', 'topup', 'add', 'topup ewallet'] 
            : ['pengeluaran', 'transfer', 'send', 'exchange', 'send-nasional', 'send-internasional'];

        $transaksiMingguIni = Transaksi::where('ID_User', $userId)
            ->whereIn('jenis_transaksi', $kategoriKueri)
            ->where('status_transaksi', 'success') 
            ->get(); // Anda dapat menambahkan scope kueri lokal sesuai kebutuhan sistem

        $dataChartFormat = [
            'Mon' => 0, 'Tue' => 0, 'Wed' => 0, 
            'Thu' => 0, 'Fri' => 0, 'Sat' => 0, 'Sun' => 0,
        ];

        $totalNominalSeminggu = 0;

        foreach ($transaksiMingguIni as $trx) {
            $hariTrx = Carbon::parse($trx->tanggal_transaksi)->format('D'); 

            if (array_key_exists($hariTrx, $dataChartFormat)) {
                $dataChartFormat[$hariTrx] += $trx->nominal;
                $totalNominalSeminggu += $trx->nominal;
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Data insight ' . $tipe . ' berhasil dikompilasi.',
            'summary' => [
                'tipe_insight' => $tipe,
                'total_akumulasi' => $totalNominalSeminggu,
                'formatted_total' => 'Rp ' . number_format($totalNominalSeminggu, 0, ',', '.')
            ],
            'chart_data' => [
                'labels' => array_keys($dataChartFormat),
                'datasets' => array_values($dataChartFormat)
            ]
        ], 200);
    }
}