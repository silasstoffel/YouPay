<?php

namespace YouPay\Operacao\Servicos\Carteira;

use YouPay\Operacao\Dominio\Carteira\AutorizadorTransferenciaServiceInterface;

class AutorizadorTransferencia implements AutorizadorTransferenciaServiceInterface
{
    public function autorizado(): bool
    {
        return true;
    }
}
