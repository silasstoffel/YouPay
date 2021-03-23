<?php

namespace YouPay\Operacao\Aplicacao\Conta;

use YouPay\Operacao\Dominio\Conta\Conta;
use YouPay\Operacao\Dominio\Conta\Eventos\Emitidos\ContaCriada;
use YouPay\Operacao\Dominio\Conta\GerenciadorSenhaInterface;
use YouPay\Operacao\Dominio\Conta\RepositorioContaInterface;
use YouPay\Operacao\Dominio\UUIDInterface;
use YouPay\Shared\Dominio\PublicadorEvento;

class CriarConta
{
    private RepositorioContaInterface $repositorioConta;
    private PublicadorEvento $publicadorEvento;

    public function __construct(
        RepositorioContaInterface $respositorioConta,
        PublicadorEvento $publicadorEvento
    )
    {
        $this->repositorioConta = $respositorioConta;
        $this->publicadorEvento = $publicadorEvento;
    }

    public function criar(
        CriarContaDto $contaDto,
        UUIDInterface $geradorUuid,
        GerenciadorSenhaInterface $gerenciadorSenha
    ) {
        $conta = $this->criarInstanciaContaPeloDto($contaDto);
        $conta->checkDuplicidadeConta($this->repositorioConta);
        $contaCriada = $this->repositorioConta->criar(
            $conta,
            $geradorUuid,
            $gerenciadorSenha
        );

        // Publica o evento de conta criada para gerar o saldo iniciar em carteira
        $evento = new ContaCriada($contaCriada);
        $this->publicadorEvento->publicar($evento);

        return $contaCriada;
    }

    private function criarInstanciaContaPeloDto(CriarContaDto $contaDto): Conta
    {
        $conta = Conta::criarInstanciaComArgumentosViaString(
            $contaDto->getTitular(),
            $contaDto->getEmail(),
            $contaDto->getCpfCnpj(),
            $contaDto->getSenha(),
            null,
            null,
            $contaDto->getCelular()
        );
        return $conta;
    }
}
