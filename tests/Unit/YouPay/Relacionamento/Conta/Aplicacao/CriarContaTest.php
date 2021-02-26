<?php

use YouPay\Relacionamento\Aplicacao\Conta\CriarConta;
use YouPay\Relacionamento\Aplicacao\Conta\CriarContaDto;
use YouPay\Relacionamento\Dominio\Conta\Conta;
use YouPay\Relacionamento\Infra\Conta\RepositorioConta;

class CriarContaTest extends TestCase
{

    private CriarContaDto $contaDto;
    private Conta $conta;

    protected function setUp(): void
    {
        $this->iniciarContaDto();
        $this->iniciarConta();
    }

    public function testPrecisaCriaContaNormalmente()
    {
        $respositorioConta = $this->createMock(RepositorioConta::class);
        $respositorioConta->method('criar')->willReturn($this->conta);

        $criadorConta = new CriarConta($respositorioConta);
        $resultadoConta = $criadorConta->criar($this->contaDto);

        $this->assertEquals($resultadoConta->getId(), 1);
        $this->assertEquals($resultadoConta->getTitular(), '001-Conta Usuario Comum');
    }

    public function testNaoPodeCriarContaComEmailJaCadastrado()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('O e-mail informado já está sendo utilizado por conta.');

        $respositorioConta = $this->createMock(RepositorioConta::class);
        $respositorioConta->method('buscarPorEmail')->willReturn($this->conta);
        $respositorioConta->method('buscarPorCpfCnpj')->willReturn(null);

        $criadorConta = new CriarConta($respositorioConta);
        $criadorConta->criar($this->contaDto);
    }

    public function testNaoPodeCriarContaCpfCnpjJaCadastrado()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('O CPF ou CNPJ informado já está sendo utilizado por conta.');

        $respositorioConta = $this->createMock(RepositorioConta::class);
        $respositorioConta->method('buscarPorEmail')->willReturn(null);
        $respositorioConta->method('buscarPorCpfCnpj')->willReturn($this->conta);

        $criadorConta = new CriarConta($respositorioConta);
        $criadorConta->criar($this->contaDto);
    }


    private function iniciarContaDto() {
        $this->contaDto = new CriarContaDto(
            '20451246063',
            '001-Conta Usuario Comum',
            'silasstofel@gmail.com',
            'senha'
        );
    }

    private function iniciarConta() {
        $this->conta = Conta::criarInstanciaComArgumentosViaString(
            '001-Conta Usuario Comum',
            '001conta@gmail.com',
            '20451246063',
            'senha',
            new DateTimeImmutable(),
            1
        );
    }


}
