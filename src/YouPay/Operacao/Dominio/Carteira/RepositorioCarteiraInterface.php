<?php

namespace YouPay\Operacao\Dominio\Carteira;

interface RepositorioCarteiraInterface
{
    public function iniciarTransacao();
    public function finalizarTransacao();
    public function desfazerTransacao();
    public function armazenarMovimentacao(Movimentacao $mov): Movimentacao;
    public function carregarSaldoCarteira(string $contaId): float;
    public function atualizarSaldoCarteira(string $contaId, float $saldo);
}
