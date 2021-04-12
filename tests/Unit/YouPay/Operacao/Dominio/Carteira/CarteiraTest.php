<?php

use YouPay\Operacao\Dominio\Carteira\Carteira;
use YouPay\Operacao\Dominio\Carteira\Movimentacao;
use YouPay\Operacao\Dominio\Carteira\TipoOperacao;
use YouPay\Operacao\Dominio\Carteira\Transferencia;
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
        $operacao = $this->criarOperacao($this->contaLojista, $this->contaPessoa, 10);
        $carteira->executarOperacao($operacao);
    }

    public function testNaoPodeFazerTransferenciaParaPropriaConta()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('A transfêrencia precisa ser entre contas diferentes.');
        $this->expectExceptionCode(400);

        $carteira = $this->contaPessoa->getCarteira();
        $operacao = $this->criarOperacao($this->contaPessoa, $this->contaPessoa, 10);
        $carteira->executarOperacao($operacao);
    }

    public function testPrecisaTransferiarNormalmente()
    {
        $saldoContaOrigem = 100.00;
        $saldoContaDestino = 200.00;
        $valorTransferencia = 50.00;

        $respositorio = $this->criarMockRepositorioCarteira();

        $respositorio->method('carregarSaldoCarteira')
            ->will(
                $this->onConsecutiveCalls(
                    $saldoContaOrigem,
                    $saldoContaDestino,
                    $saldoContaOrigem,
                    $saldoContaDestino,
                    $saldoContaOrigem
                )
            );

        /** @var RepositorioCarteira $respositorio */
        $carteira = new Carteira($this->contaPessoa, $respositorio);

        $operacao = $this->criarOperacao(
            $this->contaPessoa,
            $this->contaLojista,
            $valorTransferencia,
            $respositorio
        );
        $carteira->executarOperacao($operacao);
        $movimentacao = $operacao->getMovimentacao();
        $this->assertEquals(100.00, $movimentacao->getSaldo());
        $this->assertEquals(50.00, $movimentacao->getValor());
        $this->assertEquals(
            TipoOperacao::DEBITO,
            $movimentacao->getTipoOperacao()
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
        $respositorio->method('carregarSaldoCarteira')
            ->will(
                $this->onConsecutiveCalls(
                    $saldoContaOrigem,
                    $saldoContaDestino,
                    $saldoContaOrigem,
                    $saldoContaDestino,
                    $saldoContaOrigem
                )
            );

        /** @var RepositorioCarteira $respositorio */
        $carteira = new Carteira($this->contaPessoa, $respositorio);

        $operacao = $this->criarOperacao(
            $this->contaPessoa,
            $this->contaLojista,
            $valorTransferencia,
            $respositorio
        );
        $carteira->executarOperacao($operacao);
    }

    public function testNaoPodeTransferiarSeAutorizadorNaoAutorizar()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Transação não autorizada.');
        $this->expectExceptionCode(400);

        $autorizador = $this->criarMockAutorizadorTransferencia(false);
        $carteira = $this->contaPessoa->getCarteira();
        $operacao = $this->criarOperacao(
            $this->contaPessoa,
            $this->contaLojista,
            10,
            null,
            $autorizador
        );
        $carteira->executarOperacao($operacao);
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
        $carteira = new Carteira($this->contaLojista, $repositorio);
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
        $carteira = new Carteira($this->contaPessoa, $repositorio);
        $this->contaPessoa->vincularCarteira($carteira);
    }

    private function criarMockRepositorioCarteira()
    {
        $repositorio = $this->createMock(RepositorioCarteira::class);
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

    private function criarMovimentacaoFake(): Movimentacao
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
        return new Movimentacao($conta, 0.01, new TipoOperacao(TipoOperacao::CREDITO));
    }

    private function criarOperacao(
        Conta $contaOrigem,
        Conta $contaDestino,
        float $valor,
        ?RepositorioCarteira $repositorio = null,
        ?AutorizadorTransferencia $autorizador = null,
        ?GeradorUuid $uuid = null
    ): Transferencia
    {
        $geradorUuid = is_null($uuid) ? new GeradorUuid : $uuid;
        if (is_null($autorizador)) {
            $autorizador = $this->criarMockAutorizadorTransferencia();
        }
        if (is_null($repositorio)) {
            $repositorio = $this->criarMockRepositorioCarteira();
        }
        return new Transferencia(
            $contaOrigem,
            $contaDestino,
            $valor,
            $repositorio,
            $autorizador,
            $geradorUuid
        );
    }

}
