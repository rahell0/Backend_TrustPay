<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopeepayAccount extends Model
{
    protected $table = 'shopeepay_accounts';

    protected $fillable = [
        'nomor_telepon',
        'nama_pemilik',
        'saldo'
    ];
}