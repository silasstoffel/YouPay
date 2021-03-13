<?php

namespace YouPay\Operacao\Dominio\Carteira;

use DateTimeImmutable;
use YouPay\Operacao\Dominio\Conta\Conta;

class Saldo
{
    private Conta $conta;
    private float $saldo = 0.00;
    private ?DateTimeImmutable $atualizadoEm = null;

    /**
     * __construct
     * @param Conta $conta conta
     * @param float $saldo saldo
     * @return void
     */
    public function __construct(
        Conta $conta,
        float $saldo = 0.00,
        ?DateTimeImmutable $atualizadoEm = null
    )
    {
        $this->atualizadoEm = $atualizadoEm;
        $this->saldo        = $saldo;
        $this->conta        = $conta;
    }

    /**
     * Obtem instancia da conta
     */
    public function getConta()
    {
        return $this->conta;
    }

    /**
     *Obtem o saldo
     */
    public function getSaldo()
    {
        return $this->saldo;
    }

    /**
     * Obtem a data de atualização
     */
    public function getAtualizadoEm(): ?DateTimeImmutable
    {
        return $this->atualizadoEm;
    }
}
