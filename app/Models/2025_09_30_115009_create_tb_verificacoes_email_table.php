<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('tb_verificacoes_email', function (Blueprint $table) {
            $table->id('id_verificacao');
            $table->unsignedBigInteger('id_usuario');
            $table->string('token')->unique();
            $table->timestamps();

            $table->foreign('id_usuario')->references('id_usuario')->on('tb_usuarios')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_verificacoes_email');
    }
};
