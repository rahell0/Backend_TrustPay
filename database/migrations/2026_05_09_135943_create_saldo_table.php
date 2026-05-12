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

            $table->string('mata_uang', 15);

            $table->integer('jumlah_saldo')->default(0);

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