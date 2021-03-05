<?php

namespace YouPay\Operacao\Dominio\Conta;

interface GerenciadorSenhaInterface
{
    /**
     * Criptografa uma senha.
     *
     * @param  string $senha
     * @return string senha criptografada
     */
    public function criptografar(string $senha): string;

    /**
     * Verificar se senha sem criptgrafia confere com a senha criptografada.
     *
     * @param  string $senhaDescriptografada senha sem a criptografia
     * @param  string $senhaCriptografada  senha com a criptografia
     * @return bool
     */
    public function verificar(string $senhaDescriptografada, string $senhaCriptografada): bool;
}
