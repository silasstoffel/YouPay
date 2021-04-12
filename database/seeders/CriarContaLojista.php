<?php

namespace Database\Seeders;

use App\Models\Conta as ContaModel;
use App\Models\Saldo;
use DateTime;
use Illuminate\Database\Seeder;
use YouPay\Operacao\Dominio\Conta\Conta;
use YouPay\Operacao\Infra\Conta\GerenciadorSenha;

class CriarContaLojista extends Seeder
{
    private $uuid = '8b04b926-2d92-4977-ab57-82a6f03ba39c';

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
        $uuid              = $this->uuid;
        $conta             = new ContaModel();
        $conta->id         = $uuid;
        $conta->cpfcnpj    = '97114152000157';
        $conta->tipo_conta = Conta::TIPO_CONTA_LOGISTA;
        $conta->titular    = 'Conta Lojista';
        $conta->email      = 'conta.lojista@youpay.com.br';
        $senha             = new GerenciadorSenha();
        $conta->hash       = $senha->criptografar('conta.lojista');
        $conta->celular    = '27988887777';
        $conta->save();
        return $conta;
    }

    private function criarSaldoCarteira(float $saldoInicial = 0)
    {
        $saldo                = new Saldo();
        $saldo->conta_id      = $this->uuid;
        $saldo->saldo         = $saldoInicial;
        $hoje                 = new DateTime();
        $saldo->atualizado_em = $hoje->format('Y-m-d H:i:s');
        $saldo->save();
    }
}
