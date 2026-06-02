<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kurs', function (Blueprint $table) {
            $table->id('id_kurs');
            $table->string('kode_valas', 5)->unique(); // USD, SGD, MYR, IDR
            $table->string('nama_valas', 50);
            $table->decimal('kurs_beli', 15, 2); // Nilai beli bank dari user
            $table->decimal('kurs_jual', 15, 2); // Nilai jual bank ke user
            $table->decimal('nilai_ke_idr', 15, 2)->nullable(); // Nilai tengah untuk backup kalkulator
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kurs');
    }
};