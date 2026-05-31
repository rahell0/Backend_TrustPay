<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_transaksi', function (Blueprint $table) {
            $table->bigIncrements('ID_Detail');
            $table->unsignedBigInteger('ID_Transaksi');
            $table->string('bank_tujuan', 50)->nullable();    
            $table->string('ewallet_tujuan', 50)->nullable(); 
            $table->string('nomor_tujuan', 30)->nullable();   
            $table->string('nama_penerima', 100);             
            $table->string('negara_tujuan', 50)->nullable();   
            $table->timestamps();

            $table->foreign('ID_Transaksi')
                  ->references('ID_Transaksi')
                  ->on('transaksi')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_transaksi');
    }
};