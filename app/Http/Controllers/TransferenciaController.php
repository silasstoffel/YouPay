<?php

namespace App\Http\Controllers;

use DomainException;
use Exception;
use Illuminate\Http\Request;
use YouPay\Operacao\Aplicacao\Carteira\Transferencia;
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

            $operacao = new Transferencia(
                new RepositorioCarteira,
                new AutorizadorTransferencia,
                new GeradorUuid,
                $contaOrigem,
                $contaDestino,
                $request->value
            );

            $operacao->executar();
            return  response()->json([], 201);
        } catch (DomainException $e) {
            return $this->responseUserError($e->getMessage());
        } catch (Exception $e) {
            return $this->responseAppError('Nao foi possível efetivar a transferência.');
        }
    }

    private function criarContaComoResposta(Conta $conta)
    {
        return [
            'id' => $conta->getId(),
        ];
    }
}
