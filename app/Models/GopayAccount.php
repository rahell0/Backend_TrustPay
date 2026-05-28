<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GopayAccount extends Model
{
    protected $table = 'gopay_accounts';

    protected $fillable = [
        'nomor_telepon',
        'nama_pemilik',
        'saldo'
    ];
}