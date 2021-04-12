<?php

namespace YouPay\Operacao\Aplicacao\Carteira;

use DomainException;
use Exception;
use YouPay\Operacao\Dominio\Carteira\AutorizadorTransferenciaServiceInterface;
use YouPay\Operacao\Dominio\Carteira\Carteira;
use YouPay\Operacao\Dominio\Carteira\Eventos\Emitidos\TransferenciaEfetivada;
use YouPay\Operacao\Dominio\Carteira\Movimentacao;
use YouPay\Operacao\Dominio\Carteira\RepositorioCarteiraInterface;
use YouPay\Operacao\Dominio\Conta\Conta;
use YouPay\Operacao\Dominio\Conta\RepositorioContaInterface;
use YouPay\Operacao\Infra\GeradorUuid;
use YouPay\Operacao\Dominio\Carteira\Transferencia as OperacaoTransferencia;
use YouPay\Shared\Dominio\PublicadorEvento;

class Transferencia
{
    private RepositorioCarteiraInterface $repositorioCarteira;
    private AutorizadorTransferenciaServiceInterface $autorizador;
    private RepositorioContaInterface $repositorioConta;
    private TransferenciaDto $transferenciaDto;
    private GeradorUuid $uuid;
    private Conta $contaOrigem;
    private Conta $contaDestino;
    private float $valor;
    private PublicadorEvento $publicadorEvento;


    /**
     * Transferencia constructor.
     * @param TransferenciaDto $transferenciaDto Dados da trânsferencia.
     * @param RepositorioCarteiraInterface $repositorioCarteira Repostiório da carteira.
     * @param RepositorioContaInterface $repositorioConta Repostitŕio de contas.
     * @param AutorizadorTransferenciaServiceInterface $autorizador Serviço autorizador de transferência.
     * @param GeradorUuid $uuid Servidor gerador de ID
     * @param PublicadorEvento $publicadorEvento Publicador de eventos
     * @throws Exception
     */
    public function __construct(
        TransferenciaDto $transferenciaDto,
        RepositorioCarteiraInterface $repositorioCarteira,
        RepositorioContaInterface $repositorioConta,
        AutorizadorTransferenciaServiceInterface $autorizador,
        GeradorUuid $uuid,
        PublicadorEvento $publicadorEvento
    )
    {
        $this->repositorioCarteira = $repositorioCarteira;
        $this->repositorioConta = $repositorioConta;
        $this->autorizador = $autorizador;
        $this->uuid = $uuid;
        $this->publicadorEvento = $publicadorEvento;
        $this->transferenciaDto = $transferenciaDto;

        $this->inicializarInstanciasDasContas();

        $this->valor = $this->transferenciaDto->getValor();
    }

    /**
     * Executa a operação se possível e lança um evento
     * do tipo TransferenciaEfetivada
     * @return Movimentacao Movimentação gerada
     * @throws DomainException
     * @throws Exception
     */
    public function executar(): Movimentacao
    {
        $operacao = new OperacaoTransferencia(
            $this->contaOrigem,
            $this->contaDestino,
            $this->valor,
            $this->repositorioCarteira,
            $this->autorizador,
            $this->uuid
        );

        $carteira = new Carteira($this->contaOrigem, $this->repositorioCarteira);
        $carteira->executarOperacao($operacao);
        $movimentacao = $operacao->getMovimentacao();

        $this->emitirEventoTransferenciaEfetivada($movimentacao);

        return $movimentacao;
    }

    /**
     * Carrega uma conta pelo ID.
     * @param string $id ID da conta
     * @return Conta|null
     */
    private function carregarContaPeloId(string $id): ?Conta
    {
        return $this->repositorioConta->buscarId($id);
    }

    /**
     * Inicializa as propiedades de contas com devidas
     * regras de validação.
     * @throws DomainException
     * @throws Exception
     */
    private function inicializarInstanciasDasContas()
    {

        $conta = $this->checkExistenciaConta(
            $this->transferenciaDto->getIdContaOrigem(),
            'Conta origem não encontrada.'
        );
        $this->contaOrigem = $conta;

        $conta = $this->checkExistenciaConta(
            $this->transferenciaDto->getIdContaDestino(),
            'Conta destino não encontrada.'
        );
        $this->contaDestino = $conta;

        if ($this->transferenciaDto->getIdContaContexto() !== $this->contaOrigem->getId()) {
            throw new DomainException('Por motivos de segurança a operação não pode ser efetivada.', 400);
        }
    }

    /**
     * Verificação se conta existe na base de contas.
     * @param string $id id da conta.
     * @param string $mensagem mensagem caso a conta não exista.
     * @return Conta conta.
     * @throws DomainException
     * @throws Exception
     */
    private function checkExistenciaConta(string $id, string $mensagem): Conta
    {
        $conta = $this->carregarContaPeloId($id);

        if (is_null($conta)) {
            throw new DomainException($mensagem, 400);
        }

        return $conta;
    }

    /**
     * Emite evento de Transferencia efetivada.
     * @param Movimentacao|null $movimentacao Movimentação
     */
    private function emitirEventoTransferenciaEfetivada(?Movimentacao $movimentacao): void
    {
        if (!is_null($movimentacao)) {
            $evento = new TransferenciaEfetivada(
                $this->contaOrigem,
                $this->contaDestino,
                $this->valor
            );
            $this->publicadorEvento->publicar($evento);
        }
    }
}
