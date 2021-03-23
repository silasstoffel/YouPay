<?php


namespace YouPay\Operacao\Dominio\Carteira;

use  DomainException;
use Exception;

interface OperacaoInterface
{
    /**
     * Executa a implementação de uma operação.
     * Exemplos: Pagamento, Transferencias, PIX e etc.
     * @throws DomainException Exceção de domínio
     * @throws Exception Exceção padrão
     */
    public function executar(): void;

    /**
     * Retorna a movimentação gerada na operação.
     * @return Movimentacao|null
     */
    public function getMovimentacao(): ?Movimentacao;
}
