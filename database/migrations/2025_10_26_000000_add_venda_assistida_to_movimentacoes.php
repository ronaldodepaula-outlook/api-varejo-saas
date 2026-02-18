<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     * Add 'venda_assistida' and cancellation origins to tb_movimentacoes.origem enum.
     */
    public function up()
    {
        DB::statement("ALTER TABLE tb_movimentacoes MODIFY origem ENUM('nota_fiscal','manual','transferencia','inventario','venda_assistida','cancelamento_venda_assistida','cancelamento_item') DEFAULT 'manual'");
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Revert to the original set (note: this will fail if rows contain the added values)
        DB::statement("ALTER TABLE tb_movimentacoes MODIFY origem ENUM('nota_fiscal','manual','transferencia','inventario') DEFAULT 'manual'");
    }
};
