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

    // RELASI USER
    public function user()
    {
        return $this->belongsTo(User::class, 'ID_User', 'ID_User');
    }
}