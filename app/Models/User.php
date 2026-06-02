<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'ID_User';

    protected $fillable = [
        'username',
        'nomor_hp',
        'password',
        'role',
        'status_operasional'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * OTOMATISASI DISCLAIMER: Memicu pembuatan saldo saat user terdaftar
     */
    protected static function booted()
    {
        static::created(function ($user) {
            // Logika ini otomatis berjalan sesaat setelah data user masuk ke database
            if ($user->role === 'user') {
                Saldo::create([
                    'ID_User'      => $user->ID_User,
                    'jumlah_saldo' => 10000000, // Rp 10.000.000,00
                    'mata_uang'    => 'IDR'
                ]);
            }
        });
    }
}