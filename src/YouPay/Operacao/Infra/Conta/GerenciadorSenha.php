<?php

namespace YouPay\Operacao\Infra\Conta;

use YouPay\Operacao\Dominio\Conta\GerenciadorSenhaInterface;

class GerenciadorSenha implements GerenciadorSenhaInterface
{
    /**
     * Criptografa uma senha.
     *
     * @param  string $senha
     * @return string senha criptografada
     */
    public function criptografar(string $senha): string
    {
        return password_hash($senha, PASSWORD_ARGON2ID);
    }

    /**
     * Verificar se senha sem criptgrafia confere com a senha criptografada.
     *
     * @param  string $senhaDescriptografada senha sem a criptografia
     * @param  string $senhaCriptografada  senha com a criptografia
     * @return bool
     */
    public function verificar(string $senhaDescriptografada, string $senhaCriptografada): bool
    {
        return password_verify($senhaDescriptografada, $senhaCriptografada);
    }
}
