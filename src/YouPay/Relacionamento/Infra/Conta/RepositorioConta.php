<?php

namespace YouPay\Relacionamento\Infra\Conta;

use YouPay\Relacionamento\Dominio\Conta\Conta;
use YouPay\Relacionamento\Dominio\Conta\RepositorioContaInterface;

class RepositorioConta implements RepositorioContaInterface
{

    /**
     * Cria uma conta
     *
     * @param  Conta $conta
     * @return Conta
     * @throws DomainException|Exception
     */
    public function criar(Conta $conta): Conta
    {
        return Conta::criarInstanciaComArgumentosViaString(
            'Silas', 'email@email.com', '09764056601', '123', null, 1
        );
    }

    /**
     * Busca um conta pelo e-mail
     *
     * @param  string $cpf
     * @return Conta|null
     */
    public function buscarPorCpfCnpj(string $cpfCnpj): ?Conta
    {
        return Conta::criarInstanciaComArgumentosViaString(
            'Silas', 'email@email.com', '09764056601', '123', null, 1
        );
    }

    /**
     * Busca um conta pelo e-mail
     *
     * @param  string $email
     * @return Conta|null
     */
    public function buscarPorEmail(string $email): ?Conta
    {
        return Conta::criarInstanciaComArgumentosViaString(
            'Silas', 'email@email.com', '09764056601', '123', null, 1
        );
    }
}
