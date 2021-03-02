<?php

namespace YouPay\Relacionamento\Infra\Conta;

use YouPay\Relacionamento\Dominio\Conta\GeradorTokenInterface;

class GeradorToken implements GeradorTokenInterface
{
    public function gerar(): string
    {
        return '';
    }
}
