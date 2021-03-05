<?php

namespace YouPay\Operacao\Dominio\Conta;

interface GeradorTokenInterface
{
    public function gerar(array $data): string;
}
