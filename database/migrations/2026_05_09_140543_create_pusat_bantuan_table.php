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
        Schema::create('pusat_bantuan', function (Blueprint $table) {
      
            $table->bigIncrements('ID_Bantuan');

            $table->unsignedBigInteger('ID_Admin');

            $table->string('kategori', 100);

            $table->string('deskripsi', 255);

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
        Schema::dropIfExists('pusat_bantuan');

        
            
    }
};
