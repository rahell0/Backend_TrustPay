<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';

    protected $primaryKey = 'ID_Transaksi';

    protected $fillable = [
        'ID_User',
        'ID_Admin',
        'jenis_transaksi',
        'nominal',
        'mata_uang',
        'status_transaksi',
        'tanggal_transaksi'
    ];

    public $timestamps = true;

    // Relasi ke Model User
    public function user()
    {
        return $this->belongsTo(User::class, 'ID_User', 'ID_User');
    }

    // Relasi ke Model DetailTransaksi
    public function detail()
    {
        return $this->hasOne(DetailTransaksi::class, 'ID_Transaksi', 'ID_Transaksi');
    }

    /**
     * Scope untuk memfilter transaksi minggu ini.
     * Menggunakan format tanggal standar database (Y-m-d H:i:s) agar akurat.
     */
    public function scopeMingguIni($query)
    {
        return $query->whereBetween('tanggal_transaksi', [
            now()->startOfWeek()->format('Y-m-d H:i:s'),
            now()->endOfWeek()->format('Y-m-d H:i:s')
        ]);
    }
}