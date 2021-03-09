<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CriarTabelaSaldo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('saldos')) {
            Schema::create('saldos', function (Blueprint $table) {
                $table->uuid('conta_id')->primary();
                $table->decimal('saldo', 10, 2);
                $table->dateTime('atualizado_em');

                $table->foreign('conta_id', 'fk_conta_saldos')
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
        Schema::dropIfExists('saldos');
    }
}
