<?php

namespace YouPay\Operacao\Dominio\Conta;
use DomainException;
use Exception;
use YouPay\Operacao\Dominio\Conta\Conta;
use YouPay\Operacao\Dominio\UUIDInterface;

interface RepositorioContaInterface
{
    /**
     * Cria uma conta
     *
     * @param  Conta $conta
     * @return Conta
     * @throws DomainException|Exception
     */
    public function criar(Conta $conta, UUIDInterface $uuid, GerenciadorSenhaInterface $gerenciadorSenha): Conta;

    /**
     * Busca por CPF CNPJ
     *
     * @param  string $cpf
     * @return YouPay\Operacao\Dominio\Conta\Conta|null
     */
    public function buscarPorCpfCnpj(string $cpf): ?Conta;

    /**
     * Busca por e-mail
     *
     * @param  string $email
     * @return YouPay\Operacao\Dominio\Conta\Conta|null
     */
    public function buscarPorEmail(string $email): ?Conta;

    /**
     * Buscar por ID
     *
     * @param  string $id
     * @return YouPay\Operacao\Dominio\Conta\Conta|null
     */
    public function buscarId(string $id): ?Conta;
}
