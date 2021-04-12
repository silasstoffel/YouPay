<?php

namespace YouPay\Operacao\Dominio\Conta;

use stdClass;

interface GerenciadorTokenInterface
{
    public function gerar(array $data): string;
    public function decodificar(string $token): ?stdClass;
}
