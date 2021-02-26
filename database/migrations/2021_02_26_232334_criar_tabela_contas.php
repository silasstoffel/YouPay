<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CriarTabelaContas extends Migration
{
    /**
     * Executa.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('cpfcnpj', 14)->unique();
            // 1 - Conta Comum | 2 - Conta Lojista
            $table->smallInteger('tipo_conta')->default(1);
            $table->string('titular', 60);
            $table->string('email', 45)->unique();
            $table->string('celular', 15);
            $table->string('hash', 80);
            $table->dateTime('criada_em', 80);
            $table->dateTime('alterada_em', 80);
        });
    }

    /**
     * Reverte.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contas');
    }
}
