<?php

namespace YouPay\Relacionamento\Infra\Conta;

use YouPay\Relacionamento\Dominio\Conta\RepositorioContaAutenticavelInterface;
use YouPay\Relacionamento\Dominio\Conta\ContaAutenticavel;

class RepositorioContaAutenticavel implements RepositorioContaAutenticavelInterface
{
    public function buscarPeloLogin($login): ?ContaAutenticavel
    {
        return null;
    }
}
