<?php

namespace YouPay\Operacao\Dominio\Conta\Eventos\Emitidos;

use DateTimeImmutable;
use YouPay\Operacao\Dominio\Conta\Conta;
use YouPay\Shared\Dominio\EventoInterface;

class ContaCriada implements EventoInterface
{
    private DateTimeImmutable $dataHora;
    private Conta $conta;

    public function __construct(Conta $conta)
    {
        $this->conta    = $conta;
        $this->dataHora = new DateTimeImmutable();
    }

    public function momento(): DateTimeImmutable
    {
        return $this->dataHora;
    }

    public function getConta(): Conta
    {
        return $this->conta;
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
