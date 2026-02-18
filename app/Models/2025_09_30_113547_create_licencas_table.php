<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// database/migrations/2025_01_01_000004_create_licencas_table.php
return new class extends Migration {
    public function up(): void {
        Schema::create('tb_licencas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('tb_empresas')->onDelete('cascade');
            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->boolean('ativa')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('tb_licencas');
    }
};

