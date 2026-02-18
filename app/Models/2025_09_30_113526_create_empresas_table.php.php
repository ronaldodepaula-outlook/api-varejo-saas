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
        Schema::create('tb_empresas', function (Blueprint $table) {
            $table->id('id_empresa');
            $table->string('nome_empresa', 150);
            $table->string('cnpj', 20)->unique()->nullable();
            $table->enum('segmento', ['varejo','industria','construcao','financeiro','marketing','outros']);
            $table->enum('status', ['pendente','ativa','inativa'])->default('pendente');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
