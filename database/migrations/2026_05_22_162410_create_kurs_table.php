<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kurs', function (Blueprint $table) {
            $table->bigIncrements('ID_Kurs');
            $table->string('kode_valas', 3)->unique(); // USD, EUR, MYR, JPY
            $table->string('nama_valas');             // US Dollar, Euro, Malaysian Ringgit, Japanese Yen
            
            // KOREKSI: Menggunakan decimal agar aman dari error "too many arguments" dan akurat untuk FinTech
            $table->decimal('nilai_ke_idr', 15, 2);   // Nilai 1 Valas dalam Rupiah (Contoh: 17096.00)
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kurs');
    }
};