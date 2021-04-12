<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

// vendor/bin/phpunit --filter 'IntegracaoAutenticacaoTest'

class IntegracaoAutenticacaoTest extends TestCase
{

    use DatabaseMigrations;

    private $conta = [
        'cpfcnpj' => '75639719000176',
        'titular' => 'Titular 001',
        'email'   => 'titular@youpay.com.br',
        'senha'   => '123456',
        'celular' => '27111122222',
    ];

    private $formLogin = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->criarContaRequest();
        $this->formLogin = [
            'login'    => $this->conta['email'],
            'password' => $this->conta['senha'],
        ];
    }

    public function testDeveLogarComEmailNormalmente()
    {
        $this->checkRespostaLoginComSucesso();
    }

    public function testDeveLogarComCpfCnpjNormalmente()
    {
        $this->formLogin['login'] = $this->conta['cpfcnpj'];
        $this->checkRespostaLoginComSucesso();
    }

    public function testNaoDeveLogarComSenhaErrada()
    {
        $this->formLogin['password'] = 'senha-errada';
        $response = $this->loginRequest();
        $response->seeStatusCode(401);
        $response->seeJson(['error' => true, 'message' => 'Senha inválida.']);
    }

    public function testNaoDeveLogarSeNaoEncontrarContra()
    {
        $this->formLogin['login'] = 'conta-que-nao-existe@youpay.com';
        $response = $this->loginRequest();
        $response->seeStatusCode(401);
        $response->seeJson(['error' => true, 'message' => 'Conta não localizada.']);
    }

    private function checkRespostaLoginComSucesso()
    {
        $response = $this->loginRequest();
        $response->seeStatusCode(200);
        $response->seeJson(['titular' => $this->conta['titular']]);
    }

    private function criarContaRequest()
    {
        $response = $this->json('POST', '/contas', $this->conta);
        return $response;
    }

    private function loginRequest()
    {
        $response = $this->json('POST', '/auth', $this->formLogin);
        return $response;
    }
}
