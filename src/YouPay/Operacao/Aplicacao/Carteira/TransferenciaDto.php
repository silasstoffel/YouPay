<?php


namespace YouPay\Operacao\Aplicacao\Carteira;


class TransferenciaDto
{
    private string $idContaOrigem;
    private string $idContaDestino;
    private float $valor;
    private string $idContaContexto;

    /**
     * TransferenciaDto constructor.
     * @param string $uuidContaOrigem
     * @param string $uuidContaDestino
     * @param float $valor
     */
    public function __construct(
        string $uuidContaOrigem,
        string $uuidContaDestino,
        float $valor,
        string $idContaContexto
    )
    {
        $this->idContaOrigem = $uuidContaOrigem;
        $this->idContaDestino = $uuidContaDestino;
        $this->valor = $valor;
        $this->idContaContexto = $idContaContexto;
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

    /**
     * @return string
     */
    public function getIdContaContexto(): string
    {
        return $this->idContaContexto;
    }


}
