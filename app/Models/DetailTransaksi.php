<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailTransaksi extends Model
{
    protected $table = 'detail_transaksi';

    protected $primaryKey = 'ID_Detail';

    protected $fillable = [

        'ID_Transaksi',
        'bank_tujuan',
        'ewallet_tujuan',
        'nama_penerima',
        'negara_tujuan'

    ];

    public $timestamps = true;

    // RELASI TRANSAKSI
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'ID_Transaksi', 'ID_Transaksi');
    }
}