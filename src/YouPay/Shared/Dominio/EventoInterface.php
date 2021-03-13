<?php

namespace YouPay\Shared\Dominio;

use DateTimeImmutable;
use JsonSerializable;

interface EventoInterface extends JsonSerializable
{
    public function momento(): DateTimeImmutable;
}
