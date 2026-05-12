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
        Schema::create('kurs_mata_uang', function (Blueprint $table) {
            
            $table->bigIncrements('ID_Kurs');

            $table->unsignedBigInteger('ID_Admin');

            $table->string('mata_uang_asal', 15);

            $table->string('mata_uang_tujuan', 15);

            $table->integer('nilai_kurs');

            $table->date('update_terbaru');

            $table->timestamps();

            $table->foreign('ID_Admin')
                  ->references('ID_Admin')
                  ->on('admin')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kurs_mata_uang');
    }
};
