<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';

    protected $primaryKey = 'ID_User';

    protected $fillable = [
        'nama',
        'email',
        'password'
    ];

    protected $hidden = [
        'password'
    ];

     // RELASI PIN
    public function pin()
    {
        return $this->hasOne(Pin::class, 'ID_User', 'ID_User');
    }

    // RELASI SALDO
    public function saldo()
    {
        return $this->hasMany(Saldo::class, 'ID_User', 'ID_User');
    }
}