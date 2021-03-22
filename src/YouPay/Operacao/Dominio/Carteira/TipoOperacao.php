<?php

namespace YouPay\Operacao\Dominio\Carteira;

use InvalidArgumentException;

class TipoOperacao
{
    const CREDITO = 'C';
    const DEBITO  = 'D';

    private string $operacao;

    public function __construct(string $operacao)
    {
        $this->checkOperacao($operacao);
        $this->operacao = $operacao;
    }

    private function eValida(string $operacao)
    {
        $operacoes = [self::CREDITO, self::DEBITO];
        return in_array($operacao, $operacoes);
    }

    private function checkOperacao(string $operacao)
    {
        if (!$this->eValida($operacao)) {
            throw new InvalidArgumentException('Argumento [string $operacao] inválido.');
        }
    }

    public function __toString()
    {
        return $this->operacao;
    }
}
