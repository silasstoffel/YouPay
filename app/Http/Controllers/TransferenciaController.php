<?php

namespace App\Http\Controllers;

use DomainException;
use Exception;
use Illuminate\Http\Request;
use YouPay\Operacao\Aplicacao\Carteira\Transferencia;
use YouPay\Operacao\Dominio\Carteira\Movimentacao;
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

            $operacao = new Transferencia(
                new RepositorioCarteira,
                new AutorizadorTransferencia,
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
            return $this->responseAppError('Nao foi possível efetivar a transferência.');
        }
    }

    private function criarRespostaMovimentacao(Movimentacao $mov)
    {
        return [
            'id'        => $mov->getId(),
            'value'     => $mov->getValor(),
            'create_at' => $mov->getDataHora()->format('Y-m-d H:i:s'),
            'payer'     => [
                'id'   => $mov->getConta()->getId(),
                'name' => $mov->getConta()->getTitular(),
                'mail' => $mov->getConta()->getEmail()->__toString(),
            ],
            'payee'     => [
                'id'   => $mov->getContaDestino()->getId(),
                'name' => $mov->getContaDestino()->getTitular(),
                'mail' => $mov->getContaDestino()->getEmail()->__toString(),
            ],
        ];
    }
}
