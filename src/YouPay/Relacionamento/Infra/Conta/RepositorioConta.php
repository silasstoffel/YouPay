<?php

namespace YouPay\Relacionamento\Infra\Conta;

use App\Models\Conta as ModelConta;
use YouPay\Relacionamento\Dominio\Conta\Conta;
use YouPay\Relacionamento\Dominio\Conta\RepositorioContaInterface;

class RepositorioConta implements RepositorioContaInterface
{

    /**
     * Cria uma conta
     *
     * @param  Conta $conta
     * @return Conta
     * @throws DomainException|Exception
     */
    public function criar(Conta $conta): Conta
    {
        $contaCriada = ModelConta::create([
            'titular'    => $conta->getTitular(),
            'cpfcnpj'    => $conta->getCpfCnpj(),
            'email'      => $conta->getEmail(),
            'tipo_conta' => $conta->getTipoConta(),
            'senha'      => $conta->getSenha(),
        ]);
        return $this->converterResultadoParaObjetoConta($contaCriada);
    }

    /**
     * Busca um conta pelo e-mail
     *
     * @param  string $cpf
     * @return Conta|null
     */
    public function buscarPorCpfCnpj(string $cpfCnpj): ?Conta
    {
        $conta = ModelConta::where('cpfcnpj', $cpfCnpj)->first();
        if ($conta) {
            return $this->converterResultadoParaObjetoConta($conta);
        }
        return null;
    }

    /**
     * Busca um conta pelo e-mail
     *
     * @param  string $email
     * @return Conta|null
     */
    public function buscarPorEmail(string $email): ?Conta
    {
        $conta = ModelConta::where('email', $email)->first();
        if ($conta) {
            return $this->converterResultadoParaObjetoConta($conta);
        }
        return null;
    }

    private function converterResultadoParaObjetoConta(\App\Models\Conta $conta): Conta
    {
        return Conta::criarInstanciaComArgumentosViaString(
            $conta->titular,
            $conta->email,
            $conta->cpfcnpj,
            $conta->senha,
            $conta->criado_em,
            $conta->id
        );
    }
}
