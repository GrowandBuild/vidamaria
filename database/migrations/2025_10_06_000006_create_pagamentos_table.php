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
        Schema::create('pagamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agendamento_id')->constrained('agendamentos')->onDelete('cascade');
            $table->foreignId('forma_pagamento_id')->constrained('formas_pagamento')->onDelete('cascade');
            $table->decimal('valor', 10, 2); // Valor pago nesta forma de pagamento
            $table->decimal('taxa', 10, 2)->default(0.00); // Valor da taxa descontada
            $table->decimal('valor_liquido', 10, 2); // Valor após taxa
            $table->decimal('valor_profissional', 10, 2); // Valor da profissional após divisão
            $table->decimal('valor_empresa', 10, 2); // Valor da empresa após divisão
            $table->decimal('gorjeta', 10, 2)->default(0.00); // Gorjeta (100% profissional)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagamentos');
    }
};









