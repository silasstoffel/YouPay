<?php

use Database\Seeders\CriarContaComum;
use Database\Seeders\CriarContaComumAdicional;
use Database\Seeders\CriarContaLojista;
use Laravel\Lumen\Testing\DatabaseMigrations;

// .\vendor\bin\phpunit --filter="TransferenciaControllerTest"

class TransferenciaControllerTest extends TestCase
{

    use DatabaseMigrations;
    private string $uuidContaComum;
    private string $uuidContaLojista;
    private array $payload = [];
    private string $uuidContaComumAdicional;

    protected function setUp(): void
    {
        parent::setUp();
        $this->criarContas();
        $this->payload = [
            'value' => 0,
            'payer' => $this->uuidContaComum,
            'payee' => $this->uuidContaLojista,
        ];
        $this->_login    = 'conta.comum@youpay.com.br';
        $this->_password = 'conta.comum';
    }

    public function testPrecisaTransferirNormamente()
    {
        $value    = 100.00;
        $response = $this->fazerTransferencia($value);
        $response->seeStatusCode(201)
            ->seeJsonContains(['value' => $value])
            ->seeJsonStructure([
                'id',
                'created_at',
                'payer' => ['id', 'name', 'email'],
                'payee' => ['id', 'name', 'email'],
            ]);
        $json = $response->response->getOriginalContent();
        $this->assertEquals($this->uuidContaComum, $json['payer']['id']);
        $this->assertEquals($this->uuidContaLojista, $json['payee']['id']);
    }

    public function testLogistaNaoPodeTransferir()
    {
        // Dados para autenticar como lojista
        $this->_login    = 'conta.lojista@youpay.com.br';
        $this->_password = 'conta.lojista';

        // Colocando o lojista como o pagador e o pagador como favorecido
        $payload                = $this->payload;
        $this->payload['payer'] = $this->uuidContaLojista;
        $this->payload['payee'] = $this->uuidContaComum;

        $value    = 100.00;
        $response = $this->fazerTransferencia($value);
        $response->seeStatusCode(400)
            ->seeJsonContains([
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
                'message' => 'Saldo insuficente para transferência.',
            ]);
    }

    public function testNaoPodeTransferirValorNegativo()
    {
        $value    = -50.99;
        $response = $this->fazerTransferencia($value);
        $response->seeStatusCode(400)
            ->seeJsonContains([
                'message' => 'Não é possível movimentar valor menor ou igual a zero.',
            ]);
    }

    public function testNaoPodeTransferirValorZerado()
    {
        $value    = 0;
        $response = $this->fazerTransferencia($value);
        $response->seeStatusCode(400)
            ->seeJsonContains([
                'message' => 'Não é possível movimentar valor menor ou igual a zero.',
            ]);
    }

    public function testNaoPodeTransferirParaContaQueNaoExiste()
    {
        $payload = $this->payload;
        $value    = 5.00;
        $this->payload['payee'] = 'id-de-conta-que-nao-existe';
        $response = $this->fazerTransferencia($value);
        $this->payload = $payload;
        $response->seeStatusCode(400)
            ->seeJsonContains([
                'message' => 'Conta destino não encontrada.',
            ]);
    }

    public function testNaoPodeTransferirSePagadorForDiferenteDaContaNoToken()
    {
        $value    = 5.00;
        // Tentando quebrar segurança, pagador do corpo do json é diferente do
        // pagador definido no token jwt.
        $this->payload['payer'] = $this->uuidContaComumAdicional;
        $response = $this->fazerTransferencia($value);
        $response->seeStatusCode(400)
            ->seeJsonContains([
                'message' => 'Por motivos de segurança a operação não pode ser efetivada.',
            ]);
    }

    private function fazerTransferencia($valor)
    {
        $payload  = array_merge($this->payload, ['value' => $valor]);
        $headers  = $this->createToken();
        $response = $this->json('POST', '/v1/operacoes/transferir', $payload, $headers);
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

        $contaComum = new CriarContaComumAdicional();
        $contaComum->run();
        $this->uuidContaComumAdicional = $contaComum->getUuid();
    }
}
