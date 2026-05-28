<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PusatBantuan extends Model
{
    protected $table = 'pusat_bantuan';
    protected $primaryKey = 'ID_Bantuan';

    protected $fillable = [
        'ID_User',
        'tipe',
        'pertanyaan_atau_subjek',
        'jawaban_atau_pesan',
        'status_keluhan'
    ];

    // Relasi ke User (untuk mengetahui siapa nasabah yang mengeluh)
    public function user()
    {
        return $this->belongsTo(User::class, 'ID_User', 'ID_User');
    }
}