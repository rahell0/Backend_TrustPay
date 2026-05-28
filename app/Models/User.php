<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'ID_User'; // Beritahu Laravel kalau primary key bukan 'id'

    protected $fillable = [
        'username',
        'nomor_hp',
        'password',
        'role'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}