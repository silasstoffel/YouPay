<?php

namespace YouPay\Operacao\Dominio\Carteira;

use DomainException;
use YouPay\Operacao\Dominio\Conta\Conta;

class Carteira
{

    private float $saldo = 0.00;

    public function __construct(float $saldo)
    {
        $this->saldo = $saldo;
    }

    public function getSaldo()
    {
        return $this->saldo;
    }

    public function transferir(Conta $contaOrigem, Conta $contaDestino, float $valor): void
    {
        if (!$contaOrigem->fazTransferencia()) {
            throw new DomainException('Esta conta não pode efetivar transferencia.', 400);
        }

        if (!$this->operacaoEntreContasDiferentes($contaOrigem, $contaDestino)) {
            throw new DomainException('A transferencia precisa ser entre contas diferentes.', 400);
        }
    }

    private function possuiSaldo(float $valor = 0.01): bool
    {
        return $this->saldo >= $valor;
    }

    private function debitar(Conta $conta)
    {

    }

    private function creditar(Conta $conta)
    {

    }

    private function operacaoEntreContasDiferentes(Conta $conta1, Conta $conta2)
    {
        return $conta1->getId() !== $conta2->getId();
    }

}
