<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pin', function (Blueprint $table) {
            $table->bigIncrements('ID_PIN');
            $table->unsignedBigInteger('ID_User');
            $table->string('Kode_PIN', 255); 
            $table->timestamps();

            // Relasi ke tabel users kolom ID_User
            $table->foreign('ID_User')
                  ->references('ID_User')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pin');
    }
};