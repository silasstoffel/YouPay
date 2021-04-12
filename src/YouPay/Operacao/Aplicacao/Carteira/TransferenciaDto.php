<?php


namespace YouPay\Operacao\Aplicacao\Carteira;


class TransferenciaDto
{
    private string $idContaOrigem;
    private string $idContaDestino;
    private float $valor;

    /**
     * TransferenciaDto constructor.
     * @param string $uuidContaOrigem
     * @param string $uuidContaDestino
     * @param float $valor
     */
    public function __construct(
        string $uuidContaOrigem,
        string $uuidContaDestino,
        float $valor
    )
    {
        $this->idContaOrigem = $uuidContaOrigem;
        $this->idContaDestino = $uuidContaDestino;
        $this->valor = $valor;
    }

    /**
     * Obtem id da conta de origem.
     * @return string
     */
    public function getIdContaOrigem(): string
    {
        return $this->idContaOrigem;
    }

    /**
     * Obtem id da conta de destino.
     * @return string
     */
    public function getIdContaDestino(): string
    {
        return $this->idContaDestino;
    }

    /**
     * Obtem o valor da operação
     * @return float
     */
    public function getValor(): float
    {
        return $this->valor;
    }
}
