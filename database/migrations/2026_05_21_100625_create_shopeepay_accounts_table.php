<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shopeepay_accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nomor_telepon')->unique(); // Berfungsi sebagai ID unik e-wallet
            $table->string('nama_pemilik');
            $table->bigInteger('saldo')->default(0); // Menggunakan bigInteger agar menampung saldo besar
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shopeepay_accounts');
    }
};