<?php

namespace App\Http\Controllers;

use DomainException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use TypeError;
use YouPay\App;
use YouPay\Operacao\Aplicacao\Carteira\Transferencia;
use YouPay\Operacao\Aplicacao\Carteira\TransferenciaDto;
use YouPay\Operacao\Dominio\Carteira\Movimentacao;
use YouPay\Operacao\Infra\Carteira\RepositorioCarteira;
use YouPay\Operacao\Infra\Conta\RepositorioConta;
use YouPay\Operacao\Infra\GeradorUuid;
use YouPay\Operacao\Servicos\Carteira\AutorizadorTransferencia;
use Illuminate\Support\Facades\DB;

class TransferenciaController extends Controller
{

    public function store(Request $request)
    {
        $url = 'https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6';
        $contaOrigem = $request->payer ?? '';
        $contaDestino = $request->payee ?? '';
        $valor = $request->value ?? 0;

        $conta = Auth::user();
        $idContaContexto = $conta->id ?? '';

        DB::beginTransaction();

        try {

            $transferencia = new TransferenciaDto(
                $contaOrigem, $contaDestino, $valor, $idContaContexto
            );

            $operacao = new Transferencia(
                $transferencia,
                new RepositorioCarteira(),
                new RepositorioConta(),
                new AutorizadorTransferencia($url),
                new GeradorUuid,
                App::getPublicadorEventos()
            );

            $mov = $operacao->executar();
            DB::commit();

            return $this->responseSuccess(
                $this->criarRespostaMovimentacao($mov),
                201
            );
        } catch (DomainException $e) {
            DB::rollBack();
            return $this->responseUserError($e->getMessage());
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseAppError('Nao foi possível efetivar a transferência.');
        } catch (TypeError $e) {
            DB::rollBack();
            // @todo: futuramente, guardar gerar log ou enviar uma mensagem para equipe do produto tratar erros dessa natureza.
            return $this->responseAppError(
                'Lamentamos, mas questões técnicas não foi possível efetivar a transferência neste momento.'
            );
        }
    }

    private function criarRespostaMovimentacao(Movimentacao $mov): array
    {
        return [
            'id' => $mov->getId(),
            'value' => $mov->getValor(),
            'created_at' => $mov->getDataHora()->format('Y-m-d H:i:s'),
            'payer' => [
                'id' => $mov->getConta()->getId(),
                'name' => $mov->getConta()->getTitular(),
                'email' => (string)$mov->getConta()->getEmail(),
            ],
            'payee' => [
                'id' => $mov->getContaDestino()->getId(),
                'name' => $mov->getContaDestino()->getTitular(),
                'email' => (string)$mov->getContaDestino()->getEmail(),
            ],
        ];
    }
}
