<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tb_usuarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->nullable()->constrained('tb_empresas')->onDelete('cascade');
            $table->string('nome');
            $table->string('email')->unique();
            $table->string('senha');
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_super_admin')->default(false); // Geral do sistema
            $table->timestamp('email_verified_at')->nullable();
            $table->string('verification_token')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('tb_usuarios');
    }
};
