<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CriarTabelaMovimentacoes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('movimentacoes')) {
            Schema::create('movimentacoes', function (Blueprint $table) {

                $table->uuid('id')->primary();
                $table->uuid('conta_id');
                $table->uuid('conta_origem')->nullable();
                $table->uuid('conta_destino')->nullable();
                $table->string('operacao', 5);
                $table->decimal('valor', 10, 2);
                $table->decimal('saldo', 10, 2)->default(0);
                $table->dateTime('criada_em');
                $table->string('descricao', 5);

                // Chaves estrangeiras
                $table->foreign('conta_id', 'fk_conta_movimentacoes')
                ->references('id')
                ->on('contas');

                $table->foreign('conta_origem', 'fk_contaorigem_em_movimentacoes')
                ->references('id')
                ->on('contas');

                $table->foreign('conta_destino', 'fk_contadestino_em_movimentacoes')
                ->references('id')
                ->on('contas');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movimentacoes');
    }
}
