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
        // Tabela pivot para múltiplos serviços por agendamento
        Schema::create('agendamento_servico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agendamento_id')->constrained('agendamentos')->onDelete('cascade');
            $table->foreignId('servico_id')->constrained('servicos')->onDelete('cascade');
            $table->decimal('preco_cobrado', 10, 2); // Preço no momento do agendamento
            $table->timestamps();
        });

        // Remover servico_id da tabela agendamentos (agora é many-to-many)
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->dropForeign(['servico_id']);
            $table->dropColumn('servico_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->foreignId('servico_id')->nullable()->constrained('servicos')->onDelete('cascade');
        });

        Schema::dropIfExists('agendamento_servico');
    }
};

