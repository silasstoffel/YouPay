<?php

namespace YouPay\Relacionamento\Infra;

use Ramsey\Uuid\Uuid;
use YouPay\Relacionamento\Dominio\UUIDInterface;

class GeradorUuid implements UUIDInterface
{
    public function gerar(): string
    {
        return Uuid::uuid4();
    }
}
