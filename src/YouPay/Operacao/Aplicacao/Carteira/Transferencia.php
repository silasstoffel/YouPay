<?php

namespace YouPay\Operacao\Aplicacao\Carteira;

use YouPay\Operacao\Dominio\Carteira\Carteira;
use YouPay\Operacao\Dominio\Carteira\Movimentacao;
use YouPay\Operacao\Dominio\Carteira\RepositorioCarteiraInterface;
use YouPay\Operacao\Dominio\Conta\Conta;
use YouPay\Operacao\Infra\GeradorUuid;
use YouPay\Operacao\Servicos\Carteira\AutorizadorTransferencia;
use YouPay\Operacao\Dominio\Carteira\Transferencia as OperacaoTransferencia;

class Transferencia
{

    private RepositorioCarteiraInterface $repositorio;
    private AutorizadorTransferencia $autorizador;
    private GeradorUuid $uuid;
    private Conta $contaOrigem;
    private Conta $contaDestino;
    private float $valor;

    public function __construct(
        RepositorioCarteiraInterface $repositorioCarteira,
        AutorizadorTransferencia $autorizador,
        GeradorUuid $uuid,
        Conta $contaOrigem,
        Conta $contaDestino,
        float $valor
    ) {
        $this->repositorio  = $repositorioCarteira;
        $this->valor        = $valor;
        $this->contaOrigem  = $contaOrigem;
        $this->contaDestino = $contaDestino;
        $this->autorizador  = $autorizador;
        $this->uuid         = $uuid;
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
        return $operacao->getMovimentacao();
    }
}
