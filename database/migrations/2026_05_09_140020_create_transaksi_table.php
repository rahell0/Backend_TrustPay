<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {

            $table->bigIncrements('ID_Transaksi');

            $table->unsignedBigInteger('ID_User');

            $table->unsignedBigInteger('ID_Admin')->nullable();

            $table->string('jenis_transaksi', 15);

            $table->integer('nominal');

            $table->string('mata_uang', 50);

            $table->enum('status_transaksi', [
                'pending',
                'disetujui',
                'ditolak'
            ]);

            $table->date('tanggal_transaksi');

            $table->timestamps();

            // FOREIGN KEY USER
            $table->foreign('ID_User')
                  ->references('ID_User')
                  ->on('users')
                  ->onDelete('cascade');

            // FOREIGN KEY ADMIN
            $table->foreign('ID_Admin')
                  ->references('ID_Admin')
                  ->on('admin')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};