<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kurs', function (Blueprint $table) {
            // 🛡️ FIX: Menyamakan nama ID_Kurs dengan properti $primaryKey di model Kurs.php
            $table->bigIncrements('ID_Kurs'); 
            $table->string('kode_valas', 5)->unique(); // USD, SGD, MYR, IDR
            $table->string('nama_valas', 50);
            $table->decimal('kurs_beli', 15, 2); 
            $table->decimal('kurs_jual', 15, 2); 
            $table->decimal('nilai_ke_idr', 15, 2)->nullable(); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kurs');
    }
};