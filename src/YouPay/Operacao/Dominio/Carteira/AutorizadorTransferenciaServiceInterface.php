<?php

namespace YouPay\Operacao\Dominio\Carteira;

interface AutorizadorTransferenciaServiceInterface
{
    public function autorizado(): bool;
}
