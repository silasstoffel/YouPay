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
            'cpfcnpj' => '09764056601',
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
            'cpfcnpj' => '09764056601',
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

    private function testNaoDeveCriarContaComCpfExistente()
    {
        $this->criarContaResponse();
        // Vai tentar criar com CPF repetido
        $this->conta['email'] = 'outro@email.com';
        $response = $this->criarContaResponse();
        $response->seeJson([
            'error' => true,
            'message' => 'O CPF ou CNPJ informado já está sendo utilizado por outra conta.'
        ]);
        $response->seeStatusCode(400);
    }

    private function criarContaResponse()
    {
        $response = $this->json('POST', '/conta', $this->conta);
        return $response;
    }
}
