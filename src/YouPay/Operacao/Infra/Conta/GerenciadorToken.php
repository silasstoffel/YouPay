<?php

namespace YouPay\Operacao\Infra\Conta;

use DateInterval;
use DateTime;
use Exception;
use Firebase\JWT\JWT;
use stdClass;
use YouPay\Operacao\Dominio\Conta\GerenciadorTokenInterface;

class GerenciadorToken implements GerenciadorTokenInterface
{
    private string $segredo;
    private int $validadeEmSegundos;

    public function __construct(string $segredo, int $validadeEmSegundos = 86400)
    {
        $this->segredo = $segredo;
        $this->validadeEmSegundos = $validadeEmSegundos;
    }

    /**
     * @throws Exception
     */
    public function gerar(array $data): string
    {
        $expiraEm = new DateTime();
        $criadoEm = $expiraEm->getTimestamp();
        $expiraEm->add(new DateInterval(sprintf('PT%sS', $this->validadeEmSegundos)));
        return JWT::encode([
            'data' => $data,
            'iat'  => $criadoEm,
            'exp'  => $expiraEm->getTimestamp(),
            'iss'  => null,
        ], $this->segredo);
    }

    public function decodificar(string $token): ?stdClass
    {
        try {
            $decoded = JWT::decode($token, $this->segredo, ['HS256']);
            if (isset($decoded->data)) {
                return $decoded->data;
            }
        } catch (Exception $ex) {
            return null;
        }
        return null;
    }
}
