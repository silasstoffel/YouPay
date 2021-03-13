<?php

namespace YouPay\Operacao\Dominio\Carteira\Eventos\Ouvintes;

use YouPay\Operacao\Dominio\Carteira\RepositorioSaldoInterface;
use YouPay\Shared\Dominio\EventoInterface;
use YouPay\Shared\Dominio\OuvinteEvento;
use YouPay\Operacao\Dominio\Carteira\Saldo;
use YouPay\Operacao\Dominio\Conta\Eventos\Emitidos\ContaCriada;

class CriarSaldoInicial extends OuvinteEvento
{
    private RepositorioSaldoInterface $respositorio;

    public function __construct(RepositorioSaldoInterface $respositorio)
    {
        $this->respositorio = $respositorio;
    }

    public function sabeProcessar(EventoInterface $evento): bool
    {
        return  $evento instanceof ContaCriada;
    }

    public function reagir(EventoInterface $evento): void
    {
        /** @var ContaCriada $evento */
        $conta = $evento->getConta();
        $saldo = new Saldo($conta, 0);
        $this->respositorio->criar($saldo);
    }
}
