<?php

namespace YouPay\Relacionamento\Dominio\Conta;

interface GeradorTokenInterface
{
    public function gerar(array $data): string;
}
