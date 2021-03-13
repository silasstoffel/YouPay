<?php

namespace YouPay\Operacao\Dominio\Carteira;

use DomainException;
use Exception;
use YouPay\Operacao\Dominio\Carteira\Movimentacao;
use YouPay\Operacao\Dominio\Conta\Conta;
use YouPay\Operacao\Dominio\UUIDInterface;

class Carteira
{

    private float $saldo = 0.00;
    private RepositorioCarteiraInterface $repositorioCarteira;
    private UUIDInterface $uuid;
    private Conta $conta;

    public function __construct(Conta $conta, RepositorioCarteiraInterface $repositorioCarteira, UUIDInterface $uuid)
    {
        $this->repositorioCarteira = $repositorioCarteira;
        $this->uuid                = $uuid;
        $this->conta               = $conta;
        // Carrega o saldo da conta da carteira
        $this->saldo = $this->carregarSaldoConta($this->conta);
    }

    public function getSaldo()
    {
        return $this->saldo;
    }

    public function transferir(
        Conta $contaDestino,
        float $valor,
        AutorizadorTransferenciaServiceInterface $autorizador
    ): Movimentacao {

        $contaOrigem = $this->conta;
        $this->validarTransferencia($contaOrigem, $contaDestino);
        $credito = $this->montarMovimentacaoCreditoTransferencia(
            $contaOrigem, $contaDestino, $valor
        );
        $debito = $this->montarMovimentacaoDebitoTransferencia(
            $contaOrigem, $contaDestino, $valor
        );

        $this->repositorioCarteira->iniciarTransacao();
        try {

            // Serviço que verifica se a transação está autorizada
            if (!$autorizador->autorizado()) {
                throw new DomainException('Transação não autorizada.', 400);
            }

            $this->repositorioCarteira->armazenarMovimentacao($credito);
            $this->creditarSaldo($contaDestino, $valor);

            $this->repositorioCarteira->armazenarMovimentacao($debito);
            $novoSaldo = $this->debitarSaldo($contaOrigem, $valor);

            // Commit
            $this->repositorioCarteira->finalizarTransacao();

            // Atualiza o saldo na carteira
            $this->saldo = $novoSaldo;
        } catch (DomainException $exc) {
            //roolback
            $this->repositorioCarteira->desfazerTransacao();
            throw $exc;
        } catch (Exception $exc) {
            $this->repositorioCarteira->desfazerTransacao();
            throw new DomainException('Não foi possível efetivar a transfência.', 400);
        }
        return $debito;
    }

    private function montarMovimentacaoCreditoTransferencia(Conta $contaOrigem, Conta $contaDestino, float $valor): Movimentacao
    {
        $operacao  = new Operacao(Operacao::CREDITO);
        $historico = sprintf('%s pagou você.', $contaOrigem->getTitular());
        $credito   = new Movimentacao(
            $contaDestino,
            $valor,
            $operacao,
            $contaOrigem,
            null,
            $historico,
            null,
            $this->uuid->gerar()
        );
        $credito->setSaldo($this->carregarSaldoConta($contaDestino));
        return $credito;
    }

    private function montarMovimentacaoDebitoTransferencia(Conta $contaOrigem, Conta $contaDestino, float $valor): Movimentacao
    {
        $historico = sprintf('Você pagou %s.', $contaDestino->getTitular());
        $operacao  = new Operacao(Operacao::DEBITO);
        $debito    = new Movimentacao(
            $contaOrigem,
            $valor,
            $operacao,
            null,
            $contaDestino,
            $historico,
            null,
            $this->uuid->gerar()
        );
        // valor do saldo de quem está transferindo tem em carteira antes da
        // trasferencia.
        $debito->setSaldo($this->saldo);
        return $debito;
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

    private function creditarSaldo(Conta $conta, float $valor): float
    {
        $saldo     = $this->repositorioCarteira->carregarSaldoCarteira($conta->getId());
        $novoSaldo = $saldo + $valor;
        $this->repositorioCarteira->atualizarSaldoCarteira($conta->getId(), $novoSaldo);
        return $novoSaldo;
    }

    private function debitarSaldo(Conta $conta, float $valor): float
    {
        $saldo = $this->repositorioCarteira->carregarSaldoCarteira($conta->getId());
        if ($valor > $saldo) {
            throw new DomainException('Saldo insuficente para transferência.', 400);
        }
        $novoSaldo = $saldo - $valor;
        $this->repositorioCarteira->atualizarSaldoCarteira($conta->getId(), $novoSaldo);
        return $novoSaldo;
    }

    private function carregarSaldoConta(Conta $conta)
    {
        return $this->repositorioCarteira->carregarSaldoCarteira($conta->getId());
    }

}
