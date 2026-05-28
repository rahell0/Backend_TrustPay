<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ValasAccount extends Model {
    protected $table = 'valas_accounts';
    protected $fillable = ['negara_tujuan', 'mata_uang', 'nomor_rekening', 'nama_penerima', 'routing_number', 'saldo_valas'];
}