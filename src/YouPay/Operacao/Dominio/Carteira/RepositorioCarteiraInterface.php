<?php

namespace YouPay\Operacao\Dominio\Carteira;

interface RepositorioCarteiraInterface
{

    public function iniciarTransacao();
    public function finalizarTransacao();
    public function desfazerTransacao();
    public function armazenar(Movimentacao $mov);
}
