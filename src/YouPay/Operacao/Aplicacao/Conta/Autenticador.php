<?php

namespace YouPay\Operacao\Aplicacao\Conta;

use DomainException;
use YouPay\Operacao\Dominio\Conta\ContaAutenticavel;
use YouPay\Operacao\Dominio\Conta\GeradorTokenInterface;
use YouPay\Operacao\Dominio\Conta\GerenciadorSenhaInterface;
use YouPay\Operacao\Dominio\Conta\RepositorioContaAutenticavelInterface;

class Autenticador
{
    private RepositorioContaAutenticavelInterface $contaAutenticavelRepositorio;
    private GeradorTokenInterface $geradorToken;
    private GerenciadorSenhaInterface $gerenciadorSenha;

    public function __construct(
        RepositorioContaAutenticavelInterface $contaAutenticavelRepositorio,
        GeradorTokenInterface $geradorToken,
        GerenciadorSenhaInterface $gerenciadorSenha
    ) {
        $this->geradorToken                 = $geradorToken;
        $this->contaAutenticavelRepositorio = $contaAutenticavelRepositorio;
        $this->gerenciadorSenha             = $gerenciadorSenha;
    }

    public function autenticar($login, $senha): ContaAutenticavel
    {
        $conta = $this->carregarConta($login);
        $conta->verificarSenha($senha, $this->gerenciadorSenha);
        $conta->criarToken($this->geradorToken);
        return $conta;
    }

    private function carregarConta($login): ContaAutenticavel
    {
        $conta = $this->contaAutenticavelRepositorio->buscarPeloLogin($login);
        if (is_null($conta)) {
            throw new DomainException('Conta não localizada.', 400);
        }
        return $conta;
    }

}
