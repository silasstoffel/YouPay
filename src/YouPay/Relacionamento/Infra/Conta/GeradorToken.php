<?php

namespace YouPay\Relacionamento\Infra\Conta;

use Firebase\JWT\JWT;
use YouPay\Relacionamento\Dominio\Conta\GeradorTokenInterface;

class GeradorToken implements GeradorTokenInterface
{

    private string $segredo;
    private int $validadeEmSegundos;

    public function __construct(string $segredo, int $validadeEmSegundos = 86400)
    {
        $this->segredo = $segredo;
        $this->validadeEmSegundos = $validadeEmSegundos;
    }

    public function gerar(array $data): string
    {
        $expiraEm = new \DateTime();
        $criadoEm = $expiraEm->getTimestamp();
        $expiraEm->add(new \DateInterval(sprintf('PT%sS', $this->validadeEmSegundos)));
        $jwt = JWT::encode([
            'data' => $data,
            'iat'  => $criadoEm,
            'exp'  => $expiraEm->getTimestamp(),
            'iss'  => null,
        ], $this->segredo);

        return $jwt;

    }

    public function getSegredo(): string
    {
        return '';
    }
}
