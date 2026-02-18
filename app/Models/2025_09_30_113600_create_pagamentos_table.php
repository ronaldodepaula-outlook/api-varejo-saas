<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// database/migrations/2025_01_01_000006_create_pagamentos_table.php
return new class extends Migration {
    public function up(): void {
        Schema::create('tb_pagamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assinatura_id')->constrained('tb_assinaturas')->onDelete('cascade');
            $table->decimal('valor', 10, 2);
            $table->string('metodo'); // Cartão, boleto, PIX
            $table->date('data_pagamento')->nullable();
            $table->string('status')->default('pendente'); // pendente, pago, falhado
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('tb_pagamentos');
    }
};
