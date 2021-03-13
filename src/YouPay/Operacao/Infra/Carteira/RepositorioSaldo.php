<?php

namespace YouPay\Operacao\Infra\Carteira;

use App\Models\Saldo as ModelsSaldo;
use DateTime;
use DateTimeImmutable;
use YouPay\Operacao\Dominio\Carteira\RepositorioSaldoInterface;
use YouPay\Operacao\Dominio\Carteira\Saldo;

class RepositorioSaldo implements RepositorioSaldoInterface
{

    /**
     * Criar registro de saldo.
     *
     * @param  Saldo $saldo
     * @return Saldo|null
     */
    public function criar(Saldo $saldo): Saldo
    {
        $model                = new ModelsSaldo();
        $model->conta_id      = $saldo->getConta()->getId();
        $model->saldo         = $saldo->getSaldo();
        $hj                   = new DateTime();
        $atualizadoEm         = $saldo->getAtualizadoEm();
        $formato              = 'Y-m-d H:i:s';
        $model->atualizado_em = !is_null($atualizadoEm) ? $atualizadoEm->format($formato) : $hj->format($formato);
        $model->save();

        $saldoCriado = new Saldo(
            $saldo->getConta(),
            $saldo->getSaldo(),
            new DateTimeImmutable($model->atualizado_em)
        );

        return $saldoCriado;
    }
}
