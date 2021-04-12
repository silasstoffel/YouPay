<?php

namespace YouPay\Operacao\Dominio\Carteira;

use DomainException;
use Exception;
use YouPay\Operacao\Dominio\Conta\Conta;

class Carteira
{
    private float $saldo = 0.00;
    private RepositorioCarteiraInterface $repositorioCarteira;
    private Conta $conta;

    public function __construct(Conta $conta, RepositorioCarteiraInterface $repositorioCarteira)
    {
        $this->repositorioCarteira = $repositorioCarteira;
        $this->conta = $conta;
        // Carrega o saldo da conta da carteira
        $this->saldo = $this->carregarSaldoConta($this->conta);
    }

    /**
     * Executa uma operação na carteira. Exemplo: crédito, debito e etc.
     *
     * @param OperacaoInterface $operacao Implementação de um operação
     * @throws DomainException
     * @throws Exception
     */
    public function executarOperacao(OperacaoInterface $operacao)
    {
        $operacao->executar();
    }

    private function carregarSaldoConta(Conta $conta): float
    {
        return $this->repositorioCarteira->carregarSaldoCarteira($conta->getId());
    }

}
