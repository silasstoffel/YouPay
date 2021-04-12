<?php


namespace YouPay\Operacao\Dominio\Carteira;


use DomainException;
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

    /**
     * Transferencia constructor.
     * @param Conta $contaOrigem Conta de origem.
     * @param Conta $contaDestino Conta de destino.
     * @param float $valor valor.
     * @param RepositorioCarteiraInterface $repositorioCarteira Repositorio carteira
     * @param AutorizadorTransferenciaServiceInterface $autorizadorTransferencia Serviço autorizador de transferência.
     * @param UUIDInterface $geradorUuid Serviço gerador ID.
     */
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
        $this->setValor($valor);
        $this->repositorioCarteira = $repositorioCarteira;
        $this->uuid = $geradorUuid;
        $this->autorizador = $autorizadorTransferencia;
    }

    /**
     * Executa uma transfencia entre contas.
     */
    public function executar(): void
    {
        $this->movimentacao = $this->transferir();
    }

    /**
     * Obtém a movimentação.
     * @return Movimentacao|null
     */
    public function getMovimentacao(): ?Movimentacao
    {
        return $this->movimentacao;
    }

    /**
     * Executa o processo de transfêrencia.
     * @return Movimentacao
     */
    private function transferir(): Movimentacao
    {
        $contaOrigem = $this->contaOrigem;
        $contaDestino = $this->contaDestino;

        $this->validarTransferencia($contaOrigem, $contaDestino);
        $credito = $this->montarMovimentacaoCreditoTransferencia();
        $debito = $this->montarMovimentacaoDebitoTransferencia();

        // Serviço que verifica se a transação está autorizada
        if (!$this->autorizador->autorizado()) {
            throw new DomainException('Transação não autorizada.', 400);
        }

        $this->repositorioCarteira->armazenarMovimentacao($credito);
        $this->creditarSaldo($contaDestino, $this->valor);

        $this->repositorioCarteira->armazenarMovimentacao($debito);
        $this->debitarSaldo($contaOrigem, $this->valor);

        return $debito;
    }

    /**
     * Valida a transferência.
     * @param Conta $contaOrigem Conta de origem.
     * @param Conta $contaDestino Conta de destino.
     */
    private function validarTransferencia(Conta $contaOrigem, Conta $contaDestino)
    {
        if (!$contaOrigem->fazTransferencia()) {
            throw new DomainException('Esta conta não pode efetivar transferência.', 400);
        }

        if (!$this->operacaoEntreContasDiferentes($contaOrigem, $contaDestino)) {
            throw new DomainException('A transfêrencia precisa ser entre contas diferentes.', 400);
        }
    }

    /**
     * Verifica se a operação ocorre entre contas diferentes.
     * @param Conta $conta1 Conta 1
     * @param Conta $conta2 Conta 2
     * @return bool
     */
    private function operacaoEntreContasDiferentes(Conta $conta1, Conta $conta2): bool
    {
        return $conta1->getId() !== $conta2->getId();
    }

    /**
     * Constroi um objeto de movimentação para conta que irá receber
     * a tranferência.
     * @return Movimentacao
     */
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

    /**
     * Constroi um objeto de movimentação para conta que será debitado
     * o recurso (recurso).
     * @return Movimentacao
     */
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

    /**
     * Carrega o saldo da conta
     * @param Conta $conta Conta.
     * @return float
     */
    private function carregarSaldoConta(Conta $conta): float
    {
        return $this->repositorioCarteira->carregarSaldoCarteira($conta->getId());
    }

    /**
     * Credita saldo.
     * @param Conta $conta Conta.
     * @param float $valor valor.
     * @return float novo saldo
     */
    private function creditarSaldo(Conta $conta, float $valor): float
    {
        $saldo = $this->repositorioCarteira->carregarSaldoCarteira($conta->getId());
        $novoSaldo = $saldo + $valor;
        $this->repositorioCarteira->atualizarSaldoCarteira($conta->getId(), $novoSaldo);
        return $novoSaldo;
    }

    /**
     * @param Conta $conta
     * @param float $valor
     * @return float
     */
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

    /**
     * Atribui valor da operação de transferência.
     * @param float $valor valor
     * @throws DomainException
     */
    public function setValor(float $valor): void
    {
        if ($valor <= 0) {
            throw new DomainException('Não é possível movimentar valor menor ou igual a zero.', 400);
        }
        $this->valor = $valor;
    }
}
