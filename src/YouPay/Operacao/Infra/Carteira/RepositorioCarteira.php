<?php

namespace YouPay\Operacao\Infra\Carteira;

use App\Models\Movimentacao as ModelMovimentacao;
use App\Models\Saldo;
use DateTime;
use Illuminate\Support\Facades\DB;
use YouPay\Operacao\Dominio\Carteira\Movimentacao;
use YouPay\Operacao\Dominio\Carteira\RepositorioCarteiraInterface;

class RepositorioCarteira implements RepositorioCarteiraInterface
{
    public function armazenarMovimentacao(Movimentacao $mov)
    {
        $data             = new DateTime();
        $m                = new ModelMovimentacao();
        $m->id            = $mov->getId();
        $m->conta_id      = $mov->getConta()->getId();
        $m->conta_origem  = $mov->getContaOrigem()->getId();
        $m->conta_destino = $mov->getContaDestino()->getId();
        $m->operacao      = $mov->getOperacao()->__toString();
        $m->valor         = $mov->getValor();
        $m->saldo         = $mov->getSaldo();
        $m->descricao     = $mov->getDescricao();
        $m->criada_em     = $data->format('Y-m-d H:i:s');
        $m->save();
    }

    public function carregarSaldoCarteira(string $contaId): float
    {
        $carteira = $carteira = $this->carregarSaldoPeloIdConta($contaId);
        return !is_null($carteira) ? $carteira->saldo : 0;
    }

    public function atualizarSaldoCarteira(string $contaId, float $saldo)
    {
        $carteira = $this->carregarSaldoPeloIdConta($contaId);
        if (!is_null($carteira)) {
            $carteira->saldo = $saldo;
        } else {
            $carteira           = new Saldo();
            $carteira->conta_id = $contaId;
            $carteira->saldo    = $saldo;
        }
        $data                    = new DateTime();
        $carteira->atualizado_em = $data->format('Y-m-d H:i:s');
        $carteira->save();
    }

    public function iniciarTransacao()
    {
        DB::beginTransaction();
    }

    public function finalizarTransacao()
    {
        DB::commit();
    }

    public function desfazerTransacao()
    {
        DB::rollBack();
    }

    private function carregarSaldoPeloIdConta(string $contaId): ?\App\Models\Saldo
    {
        $carteira = Saldo::find($contaId);
        return (!is_null($carteira)) ? $carteira : null;
    }
}
