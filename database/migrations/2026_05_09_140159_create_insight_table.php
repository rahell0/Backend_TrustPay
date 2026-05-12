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
        Schema::create('insight', function (Blueprint $table) {
        
            $table->bigIncrements('ID_Insight');

            $table->unsignedBigInteger('ID_User');

            $table->string('periode', 50);

            $table->integer('total_pemasukan');

            $table->integer('total_pengeluaran');

            $table->date('tanggal_update');

            $table->timestamps();

            $table->foreign('ID_User')
                  ->references('ID_User')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insight');
    }
};
