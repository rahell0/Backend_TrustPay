<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
           
            $table->bigIncrements('ID_Notifikasi');

            $table->unsignedBigInteger('ID_User');

            $table->unsignedBigInteger('ID_Transaksi');

            $table->integer('nominal_mutasi');

            $table->text('isi_notifikasi');

            $table->date('tanggal_waktu');

            $table->timestamps();

            $table->foreign('ID_User')
                  ->references('ID_User')
                  ->on('users')
                  ->onDelete('cascade');

            $table->foreign('ID_Transaksi')
                  ->references('ID_Transaksi')
                  ->on('transaksi')
                  ->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};
