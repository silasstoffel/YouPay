<?php

namespace YouPay\Relacionamento\Aplicacao\Conta;

use DomainException;
use YouPay\Relacionamento\Dominio\Conta\ContaAutenticavel;
use YouPay\Relacionamento\Dominio\Conta\GeradorTokenInterface;
use YouPay\Relacionamento\Dominio\Conta\GerenciadorSenhaInterface;
use YouPay\Relacionamento\Dominio\Conta\RepositorioContaAutenticavelInterface;

class Autenticador
{
    private RepositorioContaAutenticavelInterface $contaAutenticavelRepositorio;
    private GeradorTokenInterface $geradorToken;
    private GerenciadorSenhaInterface $gerenciadorSenha;
    private int $segundosValidadeToken = 86400;

    public function __construct(
        RepositorioContaAutenticavelInterface $contaAutenticavelRepositorio,
        GeradorTokenInterface $geradorToken,
        GerenciadorSenhaInterface $gerenciadorSenha,
        int $segundosValidadeToken = 86400
    ) {
        $this->geradorToken                 = $geradorToken;
        $this->segundosValidadeToken        = $segundosValidadeToken;
        $this->contaAutenticavelRepositorio = $contaAutenticavelRepositorio;
        $this->gerenciadorSenha             = $gerenciadorSenha;
    }

    public function autenticar($login, $senha): ContaAutenticavel
    {
        $conta = $this->carregarConta($login);
        $conta->verificarSenha($senha, $this->gerenciadorSenha);
        $conta->criarToken($this->geradorToken, $this->segundosValidadeToken);
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
