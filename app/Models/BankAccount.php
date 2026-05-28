<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model {
    protected $table = 'bank_accounts';
    protected $fillable = ['nama_bank', 'nomor_rekening', 'nama_pemilik', 'saldo'];
}