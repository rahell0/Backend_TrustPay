<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kurs extends Model
{
    protected $table = 'kurs';
    protected $primaryKey = 'ID_Kurs';
    
    protected $fillable = [
        'kode_valas',
        'nama_valas',
        'nilai_ke_idr'
    ];
}