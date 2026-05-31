<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $table = 'admin'; 
    protected $primaryKey = 'ID_Admin';
    
    protected $fillable = [
        'nama_admin',
        'nomor_hp', 
        'password'
    ];
}