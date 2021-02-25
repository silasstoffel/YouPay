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

    public function testCriaNormalmente()
    {
        $respositorioConta = $this->createMock(RepositorioConta::class);
        $respositorioConta->method('criar')->willReturn($this->conta);

        $criadorConta = new CriarConta($respositorioConta);
        $resultadoConta = $criadorConta->criar($this->contaDto);

        $this->assertEquals($resultadoConta->getId(), 1);
        $this->assertEquals($resultadoConta->getTitular(), '001-Conta Usuario Comum');
    }

    public function testCriaContaComEmailInvalido()
    {
        $respositorioConta = $this->createMock(RepositorioConta::class);
        $respositorioConta->method('criar')->willReturn($this->conta);

        $criadorConta = new CriarConta($respositorioConta);
        $this->contaDto->setEmail('email-invalido.com.br');
        $resultadoConta = $criadorConta->criar($this->contaDto);

        $this->assertEquals($resultadoConta->getId(), 1);
        $this->assertEquals($resultadoConta->getTitular(), '001-Conta Usuario Comum');
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
