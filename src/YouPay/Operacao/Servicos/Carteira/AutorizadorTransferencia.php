<?php

namespace YouPay\Operacao\Servicos\Carteira;

use DomainException;
use Exception;
use GuzzleHttp\Client;
use YouPay\Operacao\Dominio\Carteira\AutorizadorTransferenciaServiceInterface;

class AutorizadorTransferencia implements AutorizadorTransferenciaServiceInterface
{
    private string $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function autorizado(): bool
    {
        return $this->verificarAutorizacao();
    }

    private function verificarAutorizacao(): bool
    {
        try {

            $http = new Client([
                'timeout' => 2.0,
                'verify'  => false,
            ]);

            $resultado = $http->get($this->url);
            $retorno   = $resultado->getBody();

            if ($resultado->getStatusCode() === 200 && strlen($retorno)) {
                $json = json_decode($retorno, true);
                if (isset($json['message']) && $json['message'] === 'Autorizado') {
                    return true;
                }
            }
        } catch (Exception $exc) {
            throw new DomainException('Não foi possível autorizar a transação.', 400);
        }

        return false;
    }
}
