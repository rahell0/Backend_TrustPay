<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('valas_accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('negara_tujuan'); // United States, Malaysia
            $table->string('mata_uang', 3); // USD, MYR
            $table->string('nomor_rekening')->unique();
            $table->string('nama_penerima');
            $table->string('routing_number')->nullable(); // Sesuai input Figma: ACH Routing Number
            $table->bigInteger('saldo_valas')->default(0); // Saldo dalam mata uang asing tersebut
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('valas_accounts'); }
};