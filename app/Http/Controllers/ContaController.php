<?php

namespace App\Http\Controllers;

use DomainException;
use Exception;
use Illuminate\Http\Request;
use YouPay\Relacionamento\Aplicacao\Conta\CriarConta;
use YouPay\Relacionamento\Aplicacao\Conta\CriarContaDto;
use YouPay\Relacionamento\Dominio\Conta\Conta;
use YouPay\Relacionamento\Infra\Conta\RepositorioConta;
use YouPay\Relacionamento\Infra\GeradorUuid;

class ContaController extends Controller
{

    public function store(Request $request)
    {
        try {
            $criadorConta = new CriarConta(new RepositorioConta());
            $uuid         = new GeradorUuid();
            $conta        = $criadorConta->criar(
                $this->criarContaDto($request),
                $uuid
            );
            return $this->responseSuccess([
                $this->criarContaComoResposta($conta),
            ], 201);
        } catch (DomainException $e) {
            return $this->responseUserError($e->getMessage());
        } catch (Exception $e) {
            return $this->responseAppError('Nao foi possível criar conta.' . $e->getMessage());
        }
    }

    private function criarContaDto(Request $request): CriarContaDto
    {
        $conta = new CriarContaDto(
            $request->cpfcnpj,
            $request->titular,
            $request->email,
            $request->senha
        );
        return $conta;
    }

    private function criarContaComoResposta(Conta $conta)
    {
        return [
            'id'         => $conta->getId(),
            'titular'    => $conta->getTitular(),
            'cpfcnpj'    => $conta->getCpfCnpj(),
            'celular'    => null,
            'email'      => $conta->getEmail(),
            'tipo_conta' => $conta->getTipoConta(),
        ];
    }
}
