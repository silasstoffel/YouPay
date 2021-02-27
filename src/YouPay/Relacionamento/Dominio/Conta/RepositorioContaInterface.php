<?php

namespace YouPay\Relacionamento\Dominio\Conta;
use DomainException;
use Exception;
use YouPay\Relacionamento\Dominio\Conta\Conta;
use YouPay\Relacionamento\Dominio\UUIDInterface;

interface RepositorioContaInterface
{
    /**
     * Cria uma conta
     *
     * @param  Conta $conta
     * @return Conta
     * @throws DomainException|Exception
     */
    public function criar(Conta $conta, UUIDInterface $uuid): Conta;

    /**
     * Busca um conta pelo e-mail
     *
     * @param  string $cpf
     * @return Conta|null
     */
    public function buscarPorCpfCnpj(string $cpf): ?Conta;

    /**
     * Busca um conta pelo e-mail
     *
     * @param  string $email
     * @return Conta|null
     */
    public function buscarPorEmail(string $email): ?Conta;
}
