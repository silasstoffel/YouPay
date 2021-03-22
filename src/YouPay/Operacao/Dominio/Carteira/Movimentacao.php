<?php

namespace YouPay\Operacao\Dominio\Carteira;

use DateTimeImmutable;
use YouPay\Operacao\Dominio\Conta\Conta;

class Movimentacao
{
    private string $id;
    private Conta $conta;
    private ?Conta $contaOrigem;
    private ?Conta $contaDestino;
    private float  $valor;
    private float  $saldo;
    private DateTimeImmutable $dataHora;
    private TipoOperacao $operacao;
    private ?string $descricao;

    public function __construct(
        Conta $conta,
        float $valor,
        TipoOperacao $operacao,
        ?Conta $contaOrigem = null,
        ?Conta $contaDestino = null,
        ?string $descricao = null,
        ?DateTimeImmutable $dataHora = null,
        ?string $id = null
    ) {
        $this->setConta($conta)
            ->setValor($valor)
            ->setTipoOperacao($operacao)
            ->setContaOrigem($contaOrigem)
            ->setContaDestino($contaDestino)
            ->setDescricao($descricao);

        if ($dataHora) {
            $this->setDataHora($dataHora);
        }

        if (strlen($id)) {
            $this->setId($id);
        }
    }

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of valor
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * Set the value of valor
     *
     * @return  self
     */
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get the value of dataHora
     */
    public function getDataHora()
    {
        return $this->dataHora;
    }

    /**
     * Set the value of dataHora
     *
     * @return  self
     */
    public function setDataHora($dataHora)
    {
        $this->dataHora = $dataHora;

        return $this;
    }

    /**
     * Get the value of conta
     */
    public function getConta() : ?Conta
    {
        return $this->conta;
    }

    /**
     * Set the value of conta
     *
     * @return  self
     */
    public function setConta(Conta $conta)
    {
        $this->conta = $conta;

        return $this;
    }

    /**
     * Get the value of contaOrigem
     */
    public function getContaOrigem() : ?Conta
    {
        return $this->contaOrigem;
    }

    /**
     * Set the value of contaOrigem
     *
     * @return  self
     */
    public function setContaOrigem(?Conta $contaOrigem) : self
    {
        $this->contaOrigem = $contaOrigem;

        return $this;
    }

    /**
     * Get the value of contaDestino
     */
    public function getContaDestino(): ?Conta
    {
        return $this->contaDestino;
    }

    /**
     * Set the value of contaDestino
     *
     * @return  self
     */
    public function setContaDestino(?Conta $contaDestino): self
    {
        $this->contaDestino = $contaDestino;

        return $this;
    }

    /**
     * Get the value of saldo
     */
    public function getSaldo()
    {
        return $this->saldo;
    }

    /**
     * Set the value of saldo
     *
     * @return  self
     */
    public function setSaldo($saldo)
    {
        $this->saldo = $saldo;

        return $this;
    }

    /**
     * Retorna tipo de Operacao
     */
    public function getTipoOperacao(): TipoOperacao
    {
        return $this->operacao;
    }

    /**
     * Atribui o tipo de operacao
     *
     * @return  self
     */
    public function setTipoOperacao(TipoOperacao $operacao)
    {
        $this->operacao = $operacao;

        return $this;
    }

    /**
     * Descrição da movimentação
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * Atribuição da descrição
     *
     * @return  self
     */
    public function setDescricao($desc)
    {
        $this->descricao = $desc;

        return $this;
    }
}
