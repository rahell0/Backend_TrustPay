<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DanaAccount extends Model
{
    protected $table = 'dana_accounts';

    protected $fillable = [
        'nomor_telepon',
        'nama_pemilik',
        'saldo'
    ];
}