<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Saldo extends Model
{
    protected $table = 'saldo';
    protected $primaryKey = 'ID_Saldo';

    protected $fillable = [
        'ID_User',
        'jumlah_saldo',
        'mata_uang' // Wajib didaftarkan di sini
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'ID_User', 'ID_User');
    }
}