<?php


namespace YouPay\Operacao\Dominio\Carteira;


use DomainException;
use Exception;
use YouPay\Operacao\Dominio\Carteira\Movimentacao;
use YouPay\Operacao\Dominio\Carteira\TipoOperacao;
use YouPay\Operacao\Dominio\Conta\Conta;
use YouPay\Operacao\Dominio\UUIDInterface;


class Transferencia implements OperacaoInterface
{
    private Conta $contaOrigem;
    private Conta $contaDestino;
    private float $valor = 0;
    private RepositorioCarteiraInterface $repositorioCarteira;
    private AutorizadorTransferenciaServiceInterface $autorizador;
    private UUIDInterface $uuid;
    private ?Movimentacao $movimentacao = null;

    public function __construct(
        Conta $contaOrigem,
        Conta $contaDestino,
        float $valor,
        RepositorioCarteiraInterface $repositorioCarteira,
        AutorizadorTransferenciaServiceInterface $autorizadorTransferencia,
        UUIDInterface $geradorUuid
    )
    {
        $this->contaDestino = $contaDestino;
        $this->contaOrigem = $contaOrigem;
        $this->valor = $valor;
        $this->repositorioCarteira = $repositorioCarteira;
        $this->uuid = $geradorUuid;
        $this->autorizador = $autorizadorTransferencia;
    }

    /**
     * Executa uma transfencia entre contas
     */
    public function executar(): void
    {
        $this->movimentacao = $this->transferir();
    }

    public function getMovimentacao(): ?Movimentacao
    {
        return  $this->movimentacao;
    }

    private function transferir(): Movimentacao
    {

        $contaOrigem = $this->contaOrigem;
        $contaDestino = $this->contaDestino;

        $this->validarTransferencia($contaOrigem, $contaDestino);
        $credito = $this->montarMovimentacaoCreditoTransferencia();
        $debito = $this->montarMovimentacaoDebitoTransferencia();

        $this->repositorioCarteira->iniciarTransacao();
        try {

            // Serviço que verifica se a transação está autorizada
            if (!$this->autorizador->autorizado()) {
                throw new DomainException('Transação não autorizada.', 400);
            }

            $this->repositorioCarteira->armazenarMovimentacao($credito);
            $this->creditarSaldo($contaDestino, $this->valor);

            $this->repositorioCarteira->armazenarMovimentacao($debito);
            $this->debitarSaldo($contaOrigem, $this->valor);

            // Commit
            $this->repositorioCarteira->finalizarTransacao();

        } catch (DomainException $exc) {
            //roolback
            $this->repositorioCarteira->desfazerTransacao();
            throw $exc;
        } catch (Exception $exc) {
            $this->repositorioCarteira->desfazerTransacao();
            throw new DomainException('Não foi possível efetivar a transfência.' . $exc->getMessage(), 400);
        }
        return $debito;
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

    private function operacaoEntreContasDiferentes(Conta $conta1, Conta $conta2): bool
    {
        return $conta1->getId() !== $conta2->getId();
    }

    private function montarMovimentacaoCreditoTransferencia(): Movimentacao
    {
        $operacao = new TipoOperacao(TipoOperacao::CREDITO);
        $historico = sprintf('%s pagou você.', $this->contaOrigem->getTitular());
        $credito = new Movimentacao(
            $this->contaDestino,
            $this->valor,
            $operacao,
            $this->contaOrigem,
            null,
            $historico,
            null,
            $this->uuid->gerar()
        );
        $credito->setSaldo($this->carregarSaldoConta($this->contaDestino));
        return $credito;
    }

    private function montarMovimentacaoDebitoTransferencia(): Movimentacao
    {
        $historico = sprintf('Você pagou %s.', $this->contaDestino->getTitular());
        $operacao = new TipoOperacao(TipoOperacao::DEBITO);
        $debito = new Movimentacao(
            $this->contaOrigem,
            $this->valor,
            $operacao,
            null,
            $this->contaDestino,
            $historico,
            null,
            $this->uuid->gerar()
        );
        // valor do saldo de quem está transferindo tem em carteira antes da
        // trasferencia.
        $debito->setSaldo($this->carregarSaldoConta($this->contaOrigem));
        return $debito;
    }

    private function carregarSaldoConta(Conta $conta): float
    {
        return $this->repositorioCarteira->carregarSaldoCarteira($conta->getId());
    }

    private function creditarSaldo(Conta $conta, float $valor): float
    {
        $saldo = $this->repositorioCarteira->carregarSaldoCarteira($conta->getId());
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

}
