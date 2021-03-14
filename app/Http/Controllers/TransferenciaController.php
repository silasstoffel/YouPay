<?php

namespace App\Http\Controllers;

use DomainException;
use Exception;
use Illuminate\Http\Request;
use TypeError;
use YouPay\Operacao\Aplicacao\Carteira\Transferencia;
use YouPay\Operacao\Dominio\Carteira\Movimentacao;
use YouPay\Operacao\Dominio\Conta\Conta;
use YouPay\Operacao\Infra\Carteira\RepositorioCarteira;
use YouPay\Operacao\Infra\Conta\RepositorioConta;
use YouPay\Operacao\Infra\GeradorUuid;
use YouPay\Operacao\Servicos\Carteira\AutorizadorTransferencia;

class TransferenciaController extends Controller
{

    public function store(Request $request)
    {
        try {
            $repositorioConta = new RepositorioConta();
            $contaOrigem      = $repositorioConta->buscarId($request->payer);
            $contaDestino     = $repositorioConta->buscarId($request->payee);
            $url              = 'https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6';

            $this->checkConta($contaOrigem, 'Conta do pagante não encontrada.');
            $this->checkConta($contaDestino, 'Conta do favorecido não encontrada.');

            $operacao = new Transferencia(
                new RepositorioCarteira,
                new AutorizadorTransferencia($url),
                new GeradorUuid,
                $contaOrigem,
                $contaDestino,
                $request->value
            );

            $mov = $operacao->executar();
            return response()->json(
                $this->criarRespostaMovimentacao($mov),
                201
            );
        } catch (DomainException $e) {
            return $this->responseUserError($e->getMessage());
        } catch (Exception $e) {
            return $this->responseAppError('Nao foi possível efetivar a transferência. ZZZ'. $e->getMessage());
        } catch (TypeError $e) {
            // @todo: futuramente, guardar gerar log ou enviar uma mensagem para equipe do produto tratar erros dessa natureza.
            return $this->responseAppError('Lamentamos, mas questões técnicas não foi possível efetivar a transferência neste momento.');
        }
    }

    private function checkConta(?Conta $conta, $mensagem = 'Conta não encontrada.')
    {
        if (!$conta instanceof Conta) {
            throw new DomainException($mensagem, 400);
        }
    }

    private function criarRespostaMovimentacao(Movimentacao $mov)
    {
        return [
            'id'         => $mov->getId(),
            'value'      => $mov->getValor(),
            'created_at' => $mov->getDataHora()->format('Y-m-d H:i:s'),
            'payer'      => [
                'id'    => $mov->getConta()->getId(),
                'name'  => $mov->getConta()->getTitular(),
                'email' => $mov->getConta()->getEmail()->__toString(),
            ],
            'payee'      => [
                'id'    => $mov->getContaDestino()->getId(),
                'name'  => $mov->getContaDestino()->getTitular(),
                'email' => $mov->getContaDestino()->getEmail()->__toString(),
            ],
        ];
    }
}
