<?php


namespace YouPay\Operacao\Dominio\Carteira\Eventos\Emitidos;


use DateTimeImmutable;
use YouPay\Shared\Dominio\EventoInterface;

class TransferenciaEfetivada implements EventoInterface
{

    public function __construct()
    {
        
    }

    public function momento(): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        // TODO: Implement jsonSerialize() method.
    }
}
