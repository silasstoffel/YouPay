<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

class IntegracaoCriarContaTest extends TestCase
{

    use DatabaseMigrations;
    //use DatabaseTransactions;

    private $conta = [];

    protected function setUp(): void
    {
        parent::setUp();
        $conta = [
            'cpfcnpj' => '75639719000176',
            'titular' => 'Titular 001',
            'email'   => 'titular@youpay.com.br',
            'senha'   => '123456',
            'celular' => '27111122222',
        ];
        $this->conta = $conta;
    }

    public function testDeveCriarContaNormalmente()
    {
        $response = $this->criarContaResponse();
        $response->seeJson([
            'cpfcnpj' => '75639719000176',
            'titular' => 'Titular 001',
            'email'   => 'titular@youpay.com.br',
            'celular' => '27111122222',
        ]);
        $response->seeStatusCode(201);
    }

    public function testNaoDeveCriarContaComEmailExistente()
    {
        $this->criarContaResponse();
        // Vai tentar criar com e-mail repetido
        $response = $this->criarContaResponse();
        $response->seeJson([
            'error' => true,
            'message' => 'O e-mail informado já está sendo utilizado por outra conta.'
        ]);
        $response->seeStatusCode(400);
    }

    public function testNaoDeveCriarContaComCpfExistente()
    {
        $this->criarContaResponse();
        // Vai tentar criar com CPF repetido
        $conta = $this->conta['email'];
        $this->conta['email'] = 'outro@email.com';
        $response = $this->criarContaResponse();
        $response->seeJson([
            'error' => true,
            'message' => 'O CPF ou CNPJ informado já está sendo utilizado por outra conta.'
        ]);
        $this->conta = $conta;
        $response->seeStatusCode(400);
    }

    public function testNaoDeveCriarContaComCpfInvalido()
    {
        $conta = $this->conta;
        $this->conta['cpfcnpj'] = '01501601778';
        $response = $this->criarContaResponse();
        $response->seeJson([
            'error' => true,
            'message' => 'CPF ou CNPJ inválido.'
        ]);
        $response->seeStatusCode(400);
        $this->conta = $conta;
    }

    public function testNaoDeveCriarContaComEmailInvalido()
    {
        $conta = $this->conta;
        $this->conta['email'] = 'invalido.email.com';
        $response = $this->criarContaResponse();
        $response->seeJson([
            'error' => true,
            'message' => 'Endereço de e-mail inválido.'
        ]);
        $response->seeStatusCode(400);
        $this->conta = $conta;
    }

    private function criarContaResponse()
    {
        $response = $this->json('POST', '/contas', $this->conta);
        return $response;
    }
}
