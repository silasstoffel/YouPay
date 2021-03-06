<?php

use YouPay\Operacao\Dominio\Carteira\Carteira;
use YouPay\Operacao\Dominio\Conta\Conta;

class CarteiraTest extends TestCase
{

    private Conta $contaLojista;
    private Conta $contaPessoa;

    protected function setUp(): void
    {
        parent::setUp();
        $this->criarContaLojista();
        $this->criarContaUsuarioComum();
    }

    public function testLogistaNaoPodeFazerTransferencia()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Esta conta não pode efetivar transferencia.');
        $this->expectExceptionCode(400);

        $carteira = $this->contaLojista->getCarteira();
        $carteira->transferir($this->contaLojista, $this->contaPessoa, 10.00);
    }

    private function criarContaLojista()
    {
        $this->contaLojista = Conta::criarInstanciaComArgumentosViaString(
            'Lojista-001',
            'lojista-001@youpay.com.br',
            '87.736.756/0001-81',
            '123456',
            '2021-03-05 16:30:00',
            'dd49a9d1-fa61-41aa-b72d-b2b0911018dd',
            '27988776655'
        );
        $carteira = new Carteira(100.00);
        $this->contaLojista->vincularCarteira($carteira);
    }

    private function criarContaUsuarioComum()
    {
        $this->contaPessoa = Conta::criarInstanciaComArgumentosViaString(
            'Pessoa-001',
            'pessoa-001@youpay.com.br',
            '132.197.150-87',
            '654321',
            '2021-03-05 16:30:00',
            'a30f3e90-e793-4687-9667-a8b8c8d3364e',
            '27911223344'
        );

        $carteira = new Carteira(100.00);
        $this->contaPessoa->vincularCarteira($carteira);
    }

}
