<?php

use YouPay\Operacao\Aplicacao\Conta\CriarConta;
use YouPay\Operacao\Aplicacao\Conta\CriarContaDto;
use YouPay\Operacao\Dominio\Conta\Conta;
use YouPay\Operacao\Infra\Conta\GerenciadorSenha;
use YouPay\Operacao\Infra\Conta\RepositorioConta;
use YouPay\Operacao\Infra\GeradorUuid;
use YouPay\Shared\Dominio\PublicadorEvento;

class CriarContaTest extends TestCase
{

    private CriarContaDto $contaDto;
    private Conta $conta;
    private $uuid;
    private $senha;

    protected function setUp(): void
    {
        parent::setUp();
        $this->iniciarContaDto();
        $this->iniciarConta();

        $this->uuid = $this->createMock(GeradorUuid::class);
        $this->uuid->method('gerar')
        ->willReturn('a299932f-9dcb-4928-8fd1-3911456cfcac');

        $this->senha = $this->createMock(GerenciadorSenha::class);
        $this->senha->method('criptografar')->willReturn('senha');
    }

    public function testPrecisaCriaContaNormalmente()
    {
        /** @var RepositorioConta   $repositorioConta */
        $repositorioConta = $this->mockRepositorioConta($this->conta);

        /** @var PublicadorEvento $publicador */
        $publicador = $this->mockPublicador();

        $criadorConta   = new CriarConta($repositorioConta, $publicador);
        $resultadoConta = $criadorConta->criar($this->contaDto, $this->uuid, $this->senha);

        $this->assertEquals($resultadoConta->getId(), 1);
        $this->assertEquals($resultadoConta->getTitular(), '001-Conta Usuario Comum');
    }

    public function testNaoPodeCriarContaComEmailJaCadastrado()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('O e-mail informado já está sendo utilizado por outra conta.');

        /** @var RepositorioConta   $repositorioConta */
        $repositorioConta = $this->mockRepositorioConta($this->conta, $this->conta);

        /** @var PublicadorEvento $publicador */
        $publicador = $this->mockPublicador();

        $criadorConta = new CriarConta($repositorioConta, $publicador);
        $criadorConta->criar($this->contaDto, $this->uuid, $this->senha);
    }

    public function testNaoPodeCriarContaCpfCnpjJaCadastrado()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('O CPF ou CNPJ informado já está sendo utilizado por outra conta.');

        /** @var  RepositorioConta $repositorioConta */
        $repositorioConta = $this->mockRepositorioConta($this->conta, null, $this->conta);

        /** @var PublicadorEvento $publicador */
        $publicador = $this->mockPublicador();

        $criadorConta = new CriarConta($repositorioConta, $publicador);
        $criadorConta->criar($this->contaDto, $this->uuid, $this->senha);
    }

    private function iniciarContaDto()
    {
        $this->contaDto = new CriarContaDto(
            '20451246063',
            '001-Conta Usuario Comum',
            'silasstofel@gmail.com',
            'senha'
        );
    }

    private function iniciarConta()
    {
        $this->conta = Conta::criarInstanciaComArgumentosViaString(
            '001-Conta Usuario Comum',
            '001conta@gmail.com',
            '20451246063',
            'senha',
            '2021-01-01 08:30:00',
            1
        );
    }

    private function mockPublicador()
    {
        $publicador = $this->createMock(PublicadorEvento::class);
        $publicador->method('publicar')->willReturn(null);
        return $publicador;
    }

    private function mockRepositorioConta($retornoAoCriarConta, $retornoBuscaPorEmail = null, $retornoBuscaPorCpfCnpj = null)
    {
        $repositorioConta = $this->createMock(RepositorioConta::class);
        $repositorioConta->method('criar')->willReturn($retornoAoCriarConta);
        $repositorioConta->method('buscarPorEmail')->willReturn($retornoBuscaPorEmail);
        $repositorioConta->method('buscarPorCpfCnpj')->willReturn($retornoBuscaPorCpfCnpj);
        return $repositorioConta;
    }

}
