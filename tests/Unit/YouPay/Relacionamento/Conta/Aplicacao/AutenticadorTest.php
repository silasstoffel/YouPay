<?php

use YouPay\Operacao\Aplicacao\Conta\Autenticador;
use YouPay\Operacao\Dominio\Conta\Conta;
use YouPay\Operacao\Dominio\Conta\ContaAutenticavel;
use YouPay\Operacao\Infra\Conta\GeradorToken;
use YouPay\Operacao\Infra\Conta\GerenciadorSenha;
use YouPay\Operacao\Infra\Conta\RepositorioContaAutenticavel;

class AutenticadorTest extends TestCase
{

    private $login;
    private $senha;
    private $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->login = 'conta@youpay.com.br';
        $this->senha = 'senha-youpay';
        $this->token = 'token-youpay';
    }

    public function testAutenticacaoDevePassar()
    {
        $auth = new Autenticador(
            $this->getMockContaAutenticavelRepositorio(),
            $this->getMockGeradorToken(),
            new GerenciadorSenha
        );

        $contaAuth = $auth->autenticar($this->login, $this->senha);

        $this->assertEquals($this->login, $contaAuth->getConta()->getEmail());
        $this->assertEquals(
            $this->getConta()->getTitular(),
            $contaAuth->getConta()->getTitular()
        );
        $this->assertEquals($this->token, $contaAuth->getToken());
    }

    public function testAutenticacaoDeveFalharQuandoNaoHouverUsuario()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Conta não localizada.');
        $this->expectExceptionCode(400);

        $auth = new Autenticador(
            $this->getMockContaAutenticavelRepositorio(true),
            $this->getMockGeradorToken(),
            new GerenciadorSenha
        );
        $auth->autenticar($this->login, $this->senha);
    }

    public function testAutenticacaoDeveFalharQuandoSenhaForErrada()
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Senha inválida.');
        $this->expectExceptionCode(400);

        $auth = new Autenticador(
            $this->getMockContaAutenticavelRepositorio(),
            $this->getMockGeradorToken(),
            new GerenciadorSenha
        );
        $auth->autenticar($this->login, 'senha-errada');
    }

    private function getContaAutenticavel()
    {
        return new ContaAutenticavel($this->getConta(), $this->token);
    }

    private function getConta()
    {
        $gerenciador = new GerenciadorSenha();
        $senha = $gerenciador->criptografar($this->senha);
        return Conta::criarInstanciaComArgumentosViaString(
            'Titular 001',
            $this->login,
            '27.787.550/0001-59',
            $senha,
            null,
            '102030',
            '27999998888'
        );
    }

    private function getMockContaAutenticavelRepositorio($resultadoDoMockDeveRetornarNulo = false)
    {
        $retornoMock = $resultadoDoMockDeveRetornarNulo ? null : $this->getContaAutenticavel();
        $contaAutenticavelRespo = $this->createMock(RepositorioContaAutenticavel::class);
        $contaAutenticavelRespo->expects($this->any())
            ->method('buscarPeloLogin')
            ->with($this->login)
            ->willReturn($retornoMock);
        return $contaAutenticavelRespo;
    }

    private function getMockGeradorToken()
    {
        $token = $this->createMock(GeradorToken::class);
        $token->method('gerar')
            ->willReturn($this->token);
        return $token;
    }
}
