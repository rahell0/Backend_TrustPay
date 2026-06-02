<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pusat_bantuan', function (Blueprint $table) {
            $table->bigIncrements('ID_Bantuan');
            $table->unsignedBigInteger('ID_User')->nullable(); 
            $table->enum('tipe', ['faq', 'keluhan'])->default('faq'); 
            $table->string('pertanyaan_atau_subjek'); 
            $table->text('jawaban_atau_pesan');       
            
            // 🛡️ FIX: Menyesuaikan opsi enum dengan teks status yang muncul pada visual UI Admin
            $table->enum('status_keluhan', ['Baru', 'Proses', 'Selesai'])->nullable(); 
            $table->timestamps();

            $table->foreign('ID_User')->references('ID_User')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pusat_bantuan');
    }
};