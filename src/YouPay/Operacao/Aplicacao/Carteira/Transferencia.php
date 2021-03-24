<?php

namespace YouPay\Operacao\Aplicacao\Carteira;

use YouPay\Operacao\Dominio\Carteira\Carteira;
use YouPay\Operacao\Dominio\Carteira\Eventos\Emitidos\TransferenciaEfetivada;
use YouPay\Operacao\Dominio\Carteira\Movimentacao;
use YouPay\Operacao\Dominio\Carteira\RepositorioCarteiraInterface;
use YouPay\Operacao\Dominio\Conta\Conta;
use YouPay\Operacao\Infra\GeradorUuid;
use YouPay\Operacao\Servicos\Carteira\AutorizadorTransferencia;
use YouPay\Operacao\Dominio\Carteira\Transferencia as OperacaoTransferencia;
use YouPay\Shared\Dominio\PublicadorEvento;

class Transferencia
{

    private RepositorioCarteiraInterface $repositorio;
    private AutorizadorTransferencia $autorizador;
    private GeradorUuid $uuid;
    private Conta $contaOrigem;
    private Conta $contaDestino;
    private float $valor;
    private PublicadorEvento $publicadorEvento;

    public function __construct(
        RepositorioCarteiraInterface $repositorioCarteira,
        AutorizadorTransferencia $autorizador,
        GeradorUuid $uuid,
        Conta $contaOrigem,
        Conta $contaDestino,
        float $valor,
        PublicadorEvento $publicadorEvento
    ) {
        $this->repositorio  = $repositorioCarteira;
        $this->valor        = $valor;
        $this->contaOrigem  = $contaOrigem;
        $this->contaDestino = $contaDestino;
        $this->autorizador  = $autorizador;
        $this->uuid         = $uuid;
        $this->publicadorEvento = $publicadorEvento;
    }

    /**
     * @return Movimentacao
     * @throws \Exception
     */
    public function executar(): Movimentacao
    {
        $operacao = new OperacaoTransferencia(
            $this->contaOrigem,
            $this->contaDestino,
            $this->valor,
            $this->repositorio,
            $this->autorizador,
            $this->uuid
        );

        $carteira = new Carteira($this->contaOrigem, $this->repositorio);
        $carteira->executarOperacao($operacao);
        $movimentacao = $operacao->getMovimentacao();

        if (!is_null($movimentacao)) {
            $evento = new TransferenciaEfetivada(
                $this->contaOrigem,
                $this->contaDestino,
                $this->valor
            );
            $this->publicadorEvento->publicar($evento);
        }

        return $movimentacao;
    }
}
