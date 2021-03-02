<?php

namespace App\Http\Controllers;

use DomainException;
use Exception;
use Illuminate\Http\Request;
use YouPay\Relacionamento\Aplicacao\Conta\Autenticador;
use YouPay\Relacionamento\Dominio\Conta\ContaAutenticavel;
use YouPay\Relacionamento\Infra\Conta\GeradorToken;
use YouPay\Relacionamento\Infra\Conta\GerenciadorSenha;
use YouPay\Relacionamento\Infra\Conta\RepositorioContaAutenticavel;

class AuthController extends Controller
{

    public function store(Request $request)
    {
        try {
            $auth = new Autenticador(
                new RepositorioContaAutenticavel(),
                new GeradorToken(),
                new GerenciadorSenha,
                86400
            );

            $contaAuth = $auth->autenticar($request->login, $request->password);

            return $this->responseSuccess(
                $this->criarResposta($contaAuth), 200
            );

        } catch (DomainException $e) {
            return $this->responseUserError($e->getMessage());
        } catch (Exception $e) {
            return $this->responseAppError('Nao foi possível criar conta.' . $e->getMessage());
        }
    }

    private function criarResposta(ContaAutenticavel $contaAuth)
    {
        $conta = $contaAuth->getConta();
        return [
            'user'  => [
                'id'         => $conta->getId(),
                'titular'    => $conta->getTitular(),
                'tipo_conta' => $conta->getTipoConta(),
            ],
            'token' => $contaAuth->getToken(),
        ];
    }
}
