<?php

use Database\Seeders\CriarContaComum;
use Database\Seeders\CriarContaLojista;
use Laravel\Lumen\Testing\DatabaseMigrations;

// .\vendor\bin\phpunit --filter="IntegracaoTransferenciaTest"

class IntegracaoTransferenciaTest extends TestCase
{

    use DatabaseMigrations;
    //use DatabaseTransactions;
    private $uuidContaComum;
    private $uuidContaLojista;
    private $payload = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->criarContas();
        $this->payload = [
            'value' => 0,
            'payer' => $this->uuidContaComum,
            'payee' => $this->uuidContaLojista,
        ];
    }

    public function testPrecisaTransferirNormamente()
    {
        $value    = 100.00;
        $response = $this->fazerTransferencia($value);
        $response->seeStatusCode(201)
            ->seeJsonContains([
                'value' => $value,
            ])->seeJsonStructure([
            'id',
            'created_at',
            'payer' => ['id', 'name', 'email'],
            'payee' => ['id', 'name', 'email'],
        ]);
        $json = $response->response->getOriginalContent();
        $this->assertEquals($this->uuidContaComum, $json['payer']['id']);
        $this->assertEquals($this->uuidContaLojista, $json['payee']['id']);
    }

    public function testContaLogistaNaoPodeTransferir()
    {
        // Colocando o lojista como o pagador e o pagador como favorecido
        $payload                = $this->payload;
        $this->payload['payer'] = $this->uuidContaLojista;
        $this->payload['payee'] = $this->uuidContaComum;

        $value    = 100.00;
        $response = $this->fazerTransferencia($value);
        $response->seeStatusCode(400)
            ->seeJsonContains([
                'error'   => true,
                'message' => 'Esta conta não pode efetivar transferência.',
            ]);
        $this->payload = $payload;
    }

    public function testNaoPodeTransferirSeNaoTiveSaldo()
    {
        $value    = 5000.00;
        $response = $this->fazerTransferencia($value);
        $response->seeStatusCode(400)
            ->seeJsonContains([
                'error'   => true,
                'message' => 'Saldo insuficente para transferência.',
            ]);
    }

    private function fazerTransferencia($valor)
    {
        $payload  = array_merge($this->payload, ['value' => $valor]);
        $response = $this->json('POST', '/v1/operacoes/transferir', $payload);
        return $response;
    }

    /**
     * Cria a conta do lojista e usuario comum para usarmos na transferencia.
     *
     * @return void
     */
    private function criarContas()
    {
        // Aqui será aproveitado da seeders para gente ter contas
        $contaLojista = new CriarContaLojista();
        $contaLojista->run();
        $this->uuidContaLojista = $contaLojista->getUuid();

        $contaComum = new CriarContaComum();
        $contaComum->run();
        $this->uuidContaComum = $contaComum->getUuid();
    }
}
