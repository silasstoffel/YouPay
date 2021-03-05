<?php

namespace YouPay\Operacao\Dominio\Conta;

use YouPay\Operacao\Dominio\Conta\ContaAutenticavel;

interface RepositorioContaAutenticavelInterface
{
    public function buscarPeloLogin($login): ?ContaAutenticavel;
}
