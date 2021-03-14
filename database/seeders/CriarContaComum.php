<?php

namespace Database\Seeders;

use App\Models\Conta as ContaModel;
use App\Models\Saldo;
use DateTime;
use Illuminate\Database\Seeder;
use YouPay\Operacao\Dominio\Conta\Conta;
use YouPay\Operacao\Infra\Conta\GerenciadorSenha;

class CriarContaComum extends Seeder
{
    private $uuid = 'f4ca258a-3e68-4a83-984d-02a7c8bab5c7';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $conta = ContaModel::find($this->uuid);
        if (is_null($conta)) {
            $conta = $this->criarConta();
            $this->criarSaldoCarteira();
        }
    }

    private function criarConta(): ContaModel
    {
        // Essa é conta que inicial do projeto que inicia com um saldo de R$ 500,00
        $conta             =
        $uuid              = $this->uuid;
        $conta             = new ContaModel();
        $conta->id         = $uuid;
        $conta->cpfcnpj    = '71961965038';
        $conta->tipo_conta = Conta::TIPO_CONTA_COMUM;
        $conta->titular    = 'Conta Comum';
        $conta->email      = 'conta.comum@youpay.com.br';
        $senha             = new GerenciadorSenha();
        $conta->hash       = $senha->criptografar('conta.comum');
        $conta->celular    = '27988887654';
        $conta->save();
        return $conta;
    }

    private function criarSaldoCarteira(float $saldoInicial = 500.00)
    {
        $saldo                = new Saldo();
        $saldo->conta_id      = $this->uuid;
        $saldo->saldo         = $saldoInicial;
        $hoje                 = new DateTime();
        $saldo->atualizado_em = $hoje->format('Y-m-d H:i:s');
        $saldo->save();
    }
}
