<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Saldo extends Model
{
    protected $table = 'saldo';

    protected $primaryKey = 'ID_Saldo';

    protected $fillable = [
        'ID_User',
        'mata_uang',
        'jumlah_saldo'
    ];

     // RELASI USER
    public function user()
    {
        return $this->belongsTo(User::class, 'ID_User', 'ID_User');
    }
}