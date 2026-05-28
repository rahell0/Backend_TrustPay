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
            $table->unsignedBigInteger('ID_User')->nullable(); // Mengaitkan ke nasabah yang mengirim keluhan (Null jika FAQ)
            $table->enum('tipe', ['faq', 'keluhan'])->default('faq'); // Pembeda kategori data
            $table->string('pertanyaan_atau_subjek'); // Judul FAQ atau Subjek keluhan
            $table->text('jawaban_atau_pesan');       // Isi jawaban FAQ atau isi pesan keluhan nasabah
            $table->enum('status_keluhan', ['open', 'resolved'])->nullable(); // Khusus untuk melacak status keluhan nasabah
            $table->timestamps();

            // Relasi ke tabel users
            $table->foreign('ID_User')->references('ID_User')->on('users')->onDelete('cascade');
    });
}

    public function down(): void
    {
        Schema::dropIfExists('pusat_bantuan');
    }
}; 