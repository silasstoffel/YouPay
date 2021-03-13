<?php

namespace App\Http\Controllers;

use DomainException;
use Exception;
use Illuminate\Http\Request;
use YouPay\Operacao\Aplicacao\Conta\CriarConta;
use YouPay\Operacao\Aplicacao\Conta\CriarContaDto;
use YouPay\Operacao\Dominio\Conta\Conta;
use YouPay\Operacao\Infra\Conta\GerenciadorSenha;
use YouPay\Operacao\Infra\Conta\RepositorioConta;
use YouPay\Operacao\Infra\GeradorUuid;

class ContaController extends Controller
{

    public function store(Request $request)
    {
        $publicadorEventos = \YouPay\App::getPublicadorEventos();
        try {
            $criadorConta = new CriarConta(
                new RepositorioConta(),
                $publicadorEventos
            );
            $uuid         = new GeradorUuid();
            $conta        = $criadorConta->criar(
                $this->criarContaDto($request),
                $uuid,
                new GerenciadorSenha
            );
            return $this->responseSuccess(
                $this->criarContaComoResposta($conta), 201
            );
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
            $request->senha,
            $request->celular
        );
        return $conta;
    }

    private function criarContaComoResposta(Conta $conta)
    {
        return [
            'id'         => $conta->getId(),
            'titular'    => $conta->getTitular(),
            'cpfcnpj'    => $conta->getCpfCnpj()->__toString(),
            'celular'    => $conta->getCelular(),
            'email'      => $conta->getEmail()->__toString(),
            'tipo_conta' => $conta->getTipoConta(),
        ];
    }
}
