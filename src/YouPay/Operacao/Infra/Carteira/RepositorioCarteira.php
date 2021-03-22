<?php

namespace YouPay\Operacao\Infra\Carteira;

use App\Models\Movimentacao as ModelMovimentacao;
use App\Models\Saldo;
use DateTime;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;
use YouPay\Operacao\Dominio\Carteira\Movimentacao;
use YouPay\Operacao\Dominio\Carteira\RepositorioCarteiraInterface;

class RepositorioCarteira implements RepositorioCarteiraInterface
{
    public function armazenarMovimentacao(Movimentacao $mov): Movimentacao
    {
        $data             = new DateTime();
        $m                = new ModelMovimentacao();
        $m->id            = $mov->getId();
        $m->conta_id      = $mov->getConta()->getId();
        $m->conta_origem  = !is_null($mov->getContaOrigem()) ? $mov->getContaOrigem()->getId() : null;
        $m->conta_destino = !is_null($mov->getContaDestino()) ? $mov->getContaDestino()->getId() : null;
        $m->operacao      = $mov->getTipoOperacao()->__toString();
        $m->valor         = $mov->getValor();
        $m->saldo         = $mov->getSaldo();
        $m->descricao     = $mov->getDescricao();
        $m->criada_em     = $data->format('Y-m-d H:i:s');
        $m->save();

        $mov->setDataHora(new DateTimeImmutable($m->criada_em));
        return $mov;
    }

    public function carregarSaldoCarteira(string $contaId): float
    {
        $carteira = $carteira = $this->carregarSaldoPeloIdConta($contaId);
        return !is_null($carteira) ? $carteira->saldo : 0.00;
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
