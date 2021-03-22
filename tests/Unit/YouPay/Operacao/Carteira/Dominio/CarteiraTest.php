<?php

use YouPay\Operacao\Dominio\Carteira\Carteira;
use YouPay\Operacao\Dominio\Carteira\Movimentacao;
use YouPay\Operacao\Dominio\Carteira\TipoOperacao;
use YouPay\Operacao\Dominio\Conta\Conta;
use YouPay\Operacao\Infra\Carteira\RepositorioCarteira;
use YouPay\Operacao\Infra\GeradorUuid;
use YouPay\Operacao\Servicos\Carteira\AutorizadorTransferencia;

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
        $this->expectExceptionMessage('Esta conta não pode efetivar transferência.');
        $this->expectExceptionCode(400);

        $carteira = $this->contaLojista->getCarteira();
        /** @var AutorizadorTransferencia $autorizador */
        $autorizador = $this->criarMockAutorizadorTransferencia();
        $carteira->transferir($this->contaPessoa, 10.00, $autorizador);
    }

    public function testNaoPodeFazerTransferenciaParaPropriaConta()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('A transfêrencia precisa ser entre contas diferentes.');
        $this->expectExceptionCode(400);

        $carteira = $this->contaPessoa->getCarteira();
        /** @var AutorizadorTransferencia $autorizador */
        $autorizador = $this->criarMockAutorizadorTransferencia();
        $carteira->transferir($this->contaPessoa, 10.00, $autorizador);
    }

    public function testPrecisaTransferiarNormalmente()
    {
        $saldoContaOrigem = 100.00;
        $saldoContaDestino = 200.00;
        $valorTransferencia = 50.00;

        $respositorio = $this->criarMockRepositorioCarteira();
        /**
         * mock de carregarSaldoCarteira(), ordem
         * 1. Saldo da conta que transfere
         * 2. Saldo da conta de destino
         * 3. Saldo de conta que transfere
        */
        $respositorio->method('carregarSaldoCarteira')
            ->will(
                $this->onConsecutiveCalls($saldoContaOrigem, $saldoContaDestino, $saldoContaOrigem, $saldoContaOrigem)
            );

            /** @var RepositorioCarteira $respositorio */
        $carteira = new Carteira(
            $this->contaPessoa,
            $respositorio,
            new GeradorUuid
        );
        /** @var AutorizadorTransferencia $autorizador */

        $autorizador = $this->criarMockAutorizadorTransferencia();

        $carteira->transferir(
            $this->contaLojista,
            $valorTransferencia,
            $autorizador
        );

        $this->assertEquals(
            ($saldoContaOrigem - $valorTransferencia),
            $carteira->getSaldo()
        );
    }

    public function testNaoPodeTransferiarSeNaoTiverSaldoSuficiente()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Saldo insuficente para transferência.');
        $this->expectExceptionCode(400);

        $saldoContaOrigem = 49.99;
        $saldoContaDestino = 200.00;
        $valorTransferencia = 50.00;

        $respositorio = $this->criarMockRepositorioCarteira();
        // Como há 3 chamadas do método carregarSaldoCarteira() é necessário
        // que a cada chamada retorne um valor diferente
        $respositorio->method('carregarSaldoCarteira')
            ->will(
                $this->onConsecutiveCalls($saldoContaOrigem, $saldoContaDestino, $saldoContaOrigem, $saldoContaOrigem)
            );

        /** @var RepositorioCarteira $respositorio */
        $carteira = new Carteira($this->contaPessoa, $respositorio, new GeradorUuid);
        /** @var AutorizadorTransferencia $autorizador */

        $autorizador = $this->criarMockAutorizadorTransferencia();

        $carteira->transferir(
            $this->contaLojista,
            $valorTransferencia,
            $autorizador
        );
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
        /** @var RepositorioCarteira $repositorio */
        $repositorio = $this->criarMockRepositorioCarteira();
        $carteira    = new Carteira($this->contaLojista, $repositorio, new GeradorUuid);
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
        /** @var RepositorioCarteira $repositorio */
        $repositorio = $this->criarMockRepositorioCarteira();
        $carteira    = new Carteira($this->contaPessoa, $repositorio, new GeradorUuid);
        $this->contaPessoa->vincularCarteira($carteira);
    }

    private function criarMockRepositorioCarteira()
    {
        $repositorio = $this->createMock(RepositorioCarteira::class);
        $repositorio->method('iniciarTransacao')->willReturn(null);
        $repositorio->method('finalizarTransacao')->willReturn(null);
        $repositorio->method('desfazerTransacao')->willReturn(null);
        $repositorio->method('armazenarMovimentacao')->willReturn(
            $this->criarMovimentacaoFake()
        );
        $repositorio->method('atualizarSaldoCarteira')->willReturn(null);
        return $repositorio;
    }

    private function criarMockAutorizadorTransferencia($autorizado = true)
    {
        $autorizador = $this->createMock(AutorizadorTransferencia::class);
        $autorizador->method('autorizado')->willReturn($autorizado);
        return $autorizador;
    }

    private function criarMovimentacaoFake ()
    {
        $conta = Conta::criarInstanciaComArgumentosViaString(
            'Pessoa-Fake-001',
            'pessoa-fake001@youpay.com.br',
            '132.197.150-87',
            '654321',
            '2021-03-05 16:30:00',
            'a30f3e90-e793-4687-9667-a8b8c8d3364e',
            '27911223344'
        );
        return new Movimentacao($conta, 0, new TipoOperacao(TipoOperacao::CREDITO));
    }

}
