<?php

use YouPay\Operacao\Aplicacao\Conta\CriarConta;
use YouPay\Operacao\Aplicacao\Conta\CriarContaDto;
use YouPay\Operacao\Dominio\Conta\Conta;
use YouPay\Operacao\Infra\Conta\GerenciadorSenha;
use YouPay\Operacao\Infra\Conta\RepositorioConta;
use YouPay\Operacao\Infra\GeradorUuid;

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
        $respositorioConta = $this->createMock(RepositorioConta::class);
        $respositorioConta->method('criar')->willReturn($this->conta);

        $criadorConta   = new CriarConta($respositorioConta);
        $resultadoConta = $criadorConta->criar($this->contaDto, $this->uuid, $this->senha);

        $this->assertEquals($resultadoConta->getId(), 1);
        $this->assertEquals($resultadoConta->getTitular(), '001-Conta Usuario Comum');
    }

    public function testNaoPodeCriarContaComEmailJaCadastrado()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('O e-mail informado já está sendo utilizado por outra conta.');

        /** @var  RepositorioConta $respositorioConta */
        $respositorioConta = $this->createMock(RepositorioConta::class);
        $respositorioConta->method('buscarPorEmail')->willReturn($this->conta);
        $respositorioConta->method('buscarPorCpfCnpj')->willReturn(null);

        $criadorConta = new CriarConta($respositorioConta);
        $criadorConta->criar($this->contaDto, $this->uuid, $this->senha);
    }

    public function testNaoPodeCriarContaCpfCnpjJaCadastrado()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('O CPF ou CNPJ informado já está sendo utilizado por outra conta.');
        /** @var  RepositorioConta $respositorioConta */
        $respositorioConta = $this->createMock(RepositorioConta::class);
        $respositorioConta->method('buscarPorEmail')->willReturn(null);
        $respositorioConta->method('buscarPorCpfCnpj')->willReturn($this->conta);

        $criadorConta = new CriarConta($respositorioConta);
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

}
