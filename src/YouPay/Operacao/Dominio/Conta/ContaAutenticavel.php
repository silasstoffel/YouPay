<?php

namespace YouPay\Operacao\Dominio\Conta;

use DomainException;

class ContaAutenticavel
{
    private Conta $conta;
    private string $token;
    private bool $autenticado = false;

    public function __construct(Conta $conta, string $token)
    {
        $this->setConta($conta)->setToken($token);
    }

    /**
     * Get the value of conta
     */
    public function getConta()
    {
        return $this->conta;
    }

    /**
     * Set the value of conta
     *
     * @return  self
     */
    public function setConta($conta)
    {
        $this->conta = $conta;

        return $this;
    }

    /**
     * Get the value of token
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set the value of token
     *
     * @return  self
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get the value of autenticado
     */
    public function getAutenticado()
    {
        return $this->autenticado;
    }

    public function verificarSenha(string $senha, GerenciadorSenhaInterface $gerenciadorSenha)
    {
        $senhaVerificada = $gerenciadorSenha->verificar(
            $senha,
            $this->getConta()->getSenha()
        );

        if (!$senhaVerificada) {
            $this->autenticado = false;
            throw new DomainException('Senha inválida.', 400);
        }

        $this->autenticado = true;
    }

    public function criarToken(GerenciadorTokenInterface $criadorToken)
    {
        $data = [
            'id' => $this->getConta()->getId(),
            'titular' => $this->getConta()->getTitular()
        ];
        $this->token = $criadorToken->gerar($data);
        return $this->getToken();
    }

}
