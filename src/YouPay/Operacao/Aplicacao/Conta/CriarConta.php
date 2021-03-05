<?php

namespace YouPay\Operacao\Aplicacao\Conta;

use YouPay\Operacao\Dominio\Conta\Conta;
use YouPay\Operacao\Dominio\Conta\GerenciadorSenhaInterface;
use YouPay\Operacao\Dominio\Conta\RepositorioContaInterface;
use YouPay\Operacao\Dominio\UUIDInterface;

class CriarConta
{
    private RepositorioContaInterface $respositorioConta;

    public function __construct(RepositorioContaInterface $respositorioConta)
    {
        $this->respositorioConta = $respositorioConta;
    }

    public function criar(
        CriarContaDto $contaDto,
        UUIDInterface $geradorUuid,
        GerenciadorSenhaInterface $gerenciadorSenha
    ) {
        $conta = $this->criarInstanciaContaPeloDto($contaDto);
        $conta->checkDuplicidadeConta($this->respositorioConta);
        return $this->respositorioConta->criar(
            $conta,
            $geradorUuid,
            $gerenciadorSenha
        );
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
