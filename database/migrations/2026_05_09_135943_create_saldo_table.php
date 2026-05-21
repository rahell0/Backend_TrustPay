<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saldo', function (Blueprint $table) {
            $table->bigIncrements('ID_Saldo');
            $table->unsignedBigInteger('ID_User');
            $table->integer('jumlah_saldo')->default(0);
            $table->string('mata_uang', 3)->default('IDR'); // <--- INI DIA YANG HILANG! JANGAN SAMPAI SAKTI LAGI
            $table->timestamps();

            $table->foreign('ID_User')
                  ->references('ID_User')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saldo');
    }
};