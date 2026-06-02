<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('ID_User'); 
            $table->string('username');
            $table->string('nomor_hp')->unique(); 
            $table->string('password');
            $table->enum('role', ['user', 'admin'])->default('user');
            $table->string('status_operasional')->default('Aktif');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};