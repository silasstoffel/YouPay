<?php

namespace YouPay\Operacao\Dominio\Carteira;

use DateTimeImmutable;
use DomainException;
use YouPay\Operacao\Dominio\Conta\Conta;

class Movimentacao
{
    private ?string $id;
    private Conta $conta;
    private ?Conta $contaOrigem;
    private ?Conta $contaDestino;
    private float $valor;
    private float $saldo;
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
    )
    {
        $this->setConta($conta)
            ->setValor($valor)
            ->setTipoOperacao($operacao)
            ->setContaOrigem($contaOrigem)
            ->setContaDestino($contaDestino)
            ->setDescricao($descricao)
            ->setDataHora($dataHora)
            ->setId($id);
    }


    /**
     * Obtem o id da movimentação.
     * @return string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * Atribui ID da movimentação.
     * @param null|string $id ID da movimentação
     * @return $this
     */
    public function setId(?string $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Obtem o valor da movimentação.
     * @return float
     */
    public function getValor(): float
    {
        return $this->valor;
    }

    /**
     * Atribui valor da movimentação.
     * @param float $valor valor em reais
     * @return $this
     */
    public function setValor(float $valor): self
    {
        if ($valor <= 0) {
            throw new DomainException('Operação não pode menor ou igual a zero.', 400);
        }
        $this->valor = $valor;

        return $this;
    }

    /**
     * Obtem o data da movimentação.
     * @return DateTimeImmutable
     */
    public function getDataHora(): DateTimeImmutable
    {
        return $this->dataHora;
    }

    /**
     * Atribui data hora da movimentação
     * @param null|DateTimeImmutable $dataHora data hora.
     * @return  self
     */
    public function setDataHora(?DateTimeImmutable $dataHora): self
    {
        if ($dataHora instanceof DateTimeImmutable) {
            $this->dataHora = $dataHora;
        }

        return $this;
    }

    /**
     * Obtem a conta da movimentação.
     * @return Conta|null
     */
    public function getConta(): ?Conta
    {
        return $this->conta;
    }

    /**
     * Atribui a conta da movimentação. A conta principal do evento
     * da movimentação.
     * @param Conta $conta conta.
     * @return self
     */
    public function setConta(Conta $conta): self
    {
        $this->conta = $conta;

        return $this;
    }

    /**
     * Obtem a conta da movimentação.
     * @return Conta|null
     */
    public function getContaOrigem(): ?Conta
    {
        return $this->contaOrigem;
    }

    /**
     * Atribui a conta de origem da movimentação. Por exemplo, em uma operação
     * de transferencia, qual foi a conta que originou a transferencia.
     * @param Conta|null $contaOrigem conta.
     * @return $this
     */
    public function setContaOrigem(?Conta $contaOrigem): self
    {
        $this->contaOrigem = $contaOrigem;

        return $this;
    }

    /**
     * Obtem a conta de destino.
     * @return Conta|null
     */
    public function getContaDestino(): ?Conta
    {
        return $this->contaDestino;
    }

    /**
     * Atribui a conta de destino da movimentação. Por exemplo, em uma operação
     * de transferência, qual a conta de destino do recurso.
     * @param Conta|null $contaDestino conta.
     * @return self
     */
    public function setContaDestino(?Conta $contaDestino): self
    {
        $this->contaDestino = $contaDestino;

        return $this;
    }

    /**
     * Obtem o saldo da conta. Este é o saldo da conta, não o saldo da conta de
     * origem ou destino da movimentação. Aqui é uma espécie de uma "foto" do
     * saldo no momento da operação.
     * @return float
     */
    public function getSaldo(): float
    {
        return $this->saldo;
    }

    /**
     * Atribui o saldo da conta. Este é o saldo da conta, não a conta de
     * origem ou destino da movimentação. Aqui é uma espécie de uma "foto" do
     * saldo no momento da operação.
     * @param float $saldo valor do saldo.
     * @return $this
     */
    public function setSaldo(float $saldo): self
    {
        $this->saldo = $saldo;

        return $this;
    }

    /**
     * Obtem o tipo de Operacao.
     * @return TipoOperacao
     */
    public function getTipoOperacao(): TipoOperacao
    {
        return $this->operacao;
    }

    /**
     * Atribui o tipo de operacao.
     * @param TipoOperacao $operacao
     * @return self
     */
    public function setTipoOperacao(TipoOperacao $operacao): self
    {
        $this->operacao = $operacao;

        return $this;
    }

    /**
     * Obtém a descrição da operação.
     * @return string|null
     */
    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    /**
     * Atribui a descrição da operação.
     * @param ?string $desc descrição
     * @return  self
     */
    public function setDescricao(?string $desc): self
    {
        $this->descricao = $desc;

        return $this;
    }
}
