<?php

namespace Database\Seeders;

use App\Models\Conta as ContaModel;
use App\Models\Saldo;
use DateTime;
use Illuminate\Database\Seeder;
use YouPay\Operacao\Dominio\Conta\Conta;
use YouPay\Operacao\Infra\Conta\GerenciadorSenha;

class CriarContaComumAdicional extends Seeder
{
    private $uuid = '748ca681-0965-40b2-b4d6-f1177ed3aa37';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $conta = ContaModel::find($this->uuid);
        if (is_null($conta)) {
            $this->criarConta();
            $this->criarSaldoCarteira();
        }
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    private function criarConta(): ContaModel
    {
        // Essa é conta que inicial do projeto que inicia com um saldo de R$ 500,00
        $uuid              = $this->uuid;
        $conta             = new ContaModel();
        $conta->id         = $uuid;
        $conta->cpfcnpj    = '36257593069';
        $conta->tipo_conta = Conta::TIPO_CONTA_COMUM;
        $conta->titular    = 'Conta Comum II';
        $conta->email      = 'conta.comum2@youpay.com.br';
        $senha             = new GerenciadorSenha();
        $conta->hash       = $senha->criptografar('conta.comum');
        $conta->celular    = '27988887655';
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
