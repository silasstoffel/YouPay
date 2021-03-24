<?php


namespace YouPay\Operacao\Dominio;


interface ServicoNotificadorInterface
{
    public function notificar(string $mensagem): void;
}
