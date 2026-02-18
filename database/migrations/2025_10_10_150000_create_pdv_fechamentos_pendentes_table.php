<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdvFechamentosPendentesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_pdv_fechamentos_pendentes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_caixa');
            $table->unsignedBigInteger('id_empresa')->nullable();
            $table->decimal('valor_fechamento', 12, 2)->nullable();
            $table->string('status')->default('pendente');
            $table->json('meta')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_pdv_fechamentos_pendentes');
    }
}
