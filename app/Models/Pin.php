<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pin extends Model
{
    protected $table = 'pin';

    protected $primaryKey = 'ID_PIN';

    protected $fillable = [
        'ID_User',
        'Kode_PIN'
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'ID_User', 'ID_User');
    }
}