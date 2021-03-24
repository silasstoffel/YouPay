<?php

namespace YouPay\Operacao\Servicos;


use Exception;
use GuzzleHttp\Client;
use YouPay\Operacao\Dominio\ServicoNotificadorInterface;
use Illuminate\Support\Facades\Log;

class Notificador implements ServicoNotificadorInterface
{
    public function notificar(string $mensagem): void
    {
        $url = 'https://run.mocky.io/v3/b19f7b9f-9cbf-4fc6-ad22-dc30601aec04';
        try {

            $http = new Client(['timeout' => 10.0, 'verify' => false]);
            $resultado = $http->get($url);

            $retorno = $resultado->getBody();

            if ($resultado->getStatusCode() === 200 && strlen($retorno)) {
                $json = json_decode($retorno, true);
                if (!$this->foiNotificado($json)) {
                    throw new Exception(
                        'Não foi possível notificar cliente.',
                        400
                    );
                }
            }
            Log::info($mensagem);
        } catch (Exception $exc) {
            // Gravar um log ou mandar envento para uma fila para tentar enviar
            // quando o servico estiver disponível
            Log::error('Nao foi possível notificar o cliente.' . $exc->getMessage());
        }
    }

    private function foiNotificado(array $resposta)
    {
        $existe = isset($resposta['message']);
        return $existe &&  $resposta['message'] === 'Enviado';
    }
}
