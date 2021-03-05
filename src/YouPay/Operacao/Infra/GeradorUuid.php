<?php

namespace YouPay\Operacao\Infra;

use Ramsey\Uuid\Uuid;
use YouPay\Operacao\Dominio\UUIDInterface;

class GeradorUuid implements UUIDInterface
{
    public function gerar(): string
    {
        return Uuid::uuid4();
    }
}
