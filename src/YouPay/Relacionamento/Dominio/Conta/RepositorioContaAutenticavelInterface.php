<?php

namespace YouPay\Relacionamento\Dominio\Conta;

use YouPay\Relacionamento\Dominio\Conta\ContaAutenticavel;

interface RepositorioContaAutenticavelInterface
{
    public function buscarPeloLogin($login): ?ContaAutenticavel;
}
