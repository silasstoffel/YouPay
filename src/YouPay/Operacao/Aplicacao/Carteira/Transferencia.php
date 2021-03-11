<?php

namespace YouPay\Operacao\Aplicacao\Carteira;

use YouPay\Operacao\Dominio\Carteira\Carteira;
use YouPay\Operacao\Dominio\Carteira\RepositorioCarteiraInterface;
use YouPay\Operacao\Dominio\Conta\Conta;
use YouPay\Operacao\Infra\GeradorUuid;
use YouPay\Operacao\Servicos\Carteira\AutorizadorTransferencia;

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

    public function executar()
    {
        $carteira = new Carteira(0, $this->repositorio, $this->uuid);
        $carteira->transferir(
            $this->contaOrigem,
            $this->contaDestino,
            $this->valor,
            $this->autorizador
        );
    }
}
