<?php

namespace YouPay\Operacao\Infra\Carteira;

use Illuminate\Support\Facades\DB;
use YouPay\Operacao\Dominio\Carteira\Movimentacao;
use YouPay\Operacao\Dominio\Carteira\RepositorioCarteiraInterface;

class RepositorioCarteira implements RepositorioCarteiraInterface
{
    public function armazenar(Movimentacao $mov)
    {

    }

    public function iniciarTransacao()
    {
        DB::beginTransaction();
    }

    public function finalizarTransacao()
    {
        DB::commit();
    }

    public function desfazerTransacao()
    {
        DB::rollBack();
    }
}
