<?php

namespace YouPay\Operacao\Dominio\Carteira\Eventos\Emitidos;


use YouPay\Operacao\Dominio\Conta\Conta;
use DateTimeImmutable;
use YouPay\Shared\Dominio\EventoInterface;

class TransferenciaEfetivada implements EventoInterface
{
    private Conta $contaOrigem;
    private Conta $contaDestino;
    private float $valor;
    private DateTimeImmutable $datahora;

    public function __construct(
        Conta $contaOrigem,
        Conta $contaDestino,
        float $valor
    )
    {
        $this->contaDestino = $contaDestino;
        $this->contaOrigem = $contaOrigem;
        $this->valor = $valor;
        $this->datahora = new DateTimeImmutable();
    }

    /**
     * Conta que recebeu a transferência
     * @return Conta
     */
    public function getContaDestino(): Conta
    {
        return $this->contaDestino;
    }

    /**
     * Valor da trânsferencia
     * @return float
     */
    public function getValor(): float
    {
        return $this->valor;
    }

    /**
     * Momento de origem do evento
     * @return DateTimeImmutable
     */
    public function momento(): DateTimeImmutable
    {
        return $this->datahora;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    /**
     * Conta que efetivou a transferência
     * @return Conta
     */
    public function getContaOrigem(): Conta
    {
        return $this->contaOrigem;
    }
}
