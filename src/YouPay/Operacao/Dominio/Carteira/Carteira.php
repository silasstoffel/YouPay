<?php

namespace YouPay\Operacao\Dominio\Carteira;

use DomainException;
use Exception;
use YouPay\Operacao\Dominio\Carteira\Movimentacao;
use YouPay\Operacao\Dominio\Conta\Conta;

class Carteira
{

    private float $saldo = 0.00;
    private RepositorioCarteiraInterface  $repositorioMovimentacao;

    public function __construct(float $saldo, RepositorioCarteiraInterface $repositorioMovimentacao)
    {
        $this->saldo                   = $saldo;
        $this->repositorioMovimentacao = $repositorioMovimentacao;
    }

    public function getSaldo()
    {
        return $this->saldo;
    }

    public function transferir(
        Conta $contaOrigem,
        Conta $contaDestino,
        float $valor,
        AutorizadorTransferenciaServiceInterface $autorizador
    ): void {

        $this->validarTransferencia($contaOrigem, $contaDestino);
        $credito  = $this->montarMovimentacaoCreditoTransferencia(
            $contaOrigem, $contaDestino, $valor
        );
        $debito  = $this->montarMovimentacaoDebitoTransferencia(
            $contaOrigem, $contaDestino, $valor
        );

        $this->repositorioMovimentacao->iniciarTransacao();
        try {

            // Serviço que verifica se a transação está autorizada
            if (!$autorizador->autorizado()) {
                throw new DomainException('Transação não autorizada.', 400);
            }

            $creditoCriado = $this->repositorioMovimentacao->armazenar($credito);
            $debitoCriado  = $this->repositorioMovimentacao->armazenar($debito);

            // Atualiza o saldo de quem fez a transferência
            $this->debitar($valor);

            // commit
            $this->repositorioMovimentacao->finalizarTransacao();
        } catch(DomainException $exc){
            //roolback
            $this->repositorioMovimentacao->desfazerTransacao();
            throw $exc;
        } catch (Exception $exc) {
            $this->repositorioMovimentacao->desfazerTransacao();
            throw new DomainException('Não foi possível efetivar a transfência.', 400);
        }
    }

    private function montarMovimentacaoCreditoTransferencia(Conta $contaOrigem, Conta $contaDestino, float $valor): Movimentacao
    {
        $operacao = new Operacao(Operacao::CREDITO);
        $historico = sprintf('% pagou você.', $contaOrigem->getTitular());
        $credito  = new Movimentacao(
            $contaDestino,
            $valor,
            $operacao,
            $contaOrigem,
            null,
            $historico
        );
        return $credito;
    }

    private function montarMovimentacaoDebitoTransferencia(Conta $contaOrigem, Conta $contaDestino, float $valor): Movimentacao
    {
        $historico = sprintf('Você pagou %s.', $contaDestino->getTitular());
        $operacao = new Operacao(Operacao::DEBITO);
        $debito   = new Movimentacao(
            $contaOrigem,
            $valor,
            $operacao,
            null,
            $contaDestino,
            $historico
        );
        return $debito;
    }

    private function possuiSaldo(float $valor = 0.01): bool
    {
        return $this->saldo >= $valor;
    }

    private function debitar(float $valor)
    {
        $this->saldo -= $valor;
    }

    private function creditar(Conta $conta, float $valor)
    {
        $this->saldo -= $valor;
    }

    private function operacaoEntreContasDiferentes(Conta $conta1, Conta $conta2)
    {
        return $conta1->getId() !== $conta2->getId();
    }

    private function validarTransferencia(Conta $contaOrigem, Conta $contaDestino)
    {
        if (!$contaOrigem->fazTransferencia()) {
            throw new DomainException('Esta conta não pode efetivar transferência.', 400);
        }

        if (!$this->operacaoEntreContasDiferentes($contaOrigem, $contaDestino)) {
            throw new DomainException('A transfêrencia precisa ser entre contas diferentes.', 400);
        }
    }

}
