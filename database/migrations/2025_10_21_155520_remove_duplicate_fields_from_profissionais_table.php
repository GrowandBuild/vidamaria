<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profissionais', function (Blueprint $table) {
            // Remover campos duplicados - nome e avatar ficam só no User
            $table->dropColumn(['nome', 'avatar']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profissionais', function (Blueprint $table) {
            // Reverter - adicionar campos de volta
            $table->string('nome');
            $table->string('avatar')->nullable();
        });
    }
};
