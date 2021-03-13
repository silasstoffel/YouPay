<?php

namespace YouPay\Operacao\Dominio\Carteira;

interface RepositorioSaldoInterface
{
    /**
     * Criar registro de saldo.
     *
     * @param  Saldo $saldo
     * @return Saldo
     */
    public function criar(Saldo $saldo): Saldo;

}
