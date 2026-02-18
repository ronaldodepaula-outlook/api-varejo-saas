<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdUsuarioToTbClientes extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tb_clientes', function (Blueprint $table) {
            $table->unsignedBigInteger('id_usuario')->after('id_empresa');
            $table->foreign('id_usuario')->references('id_usuario')->on('tb_usuarios');
        });

        // Atualiza registros existentes com um id_usuario padrão se necessário
        DB::statement('UPDATE tb_clientes SET id_usuario = (SELECT id_usuario FROM tb_usuarios LIMIT 1) WHERE id_usuario IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_clientes', function (Blueprint $table) {
            $table->dropForeign(['id_usuario']);
            $table->dropColumn('id_usuario');
        });
    }
}