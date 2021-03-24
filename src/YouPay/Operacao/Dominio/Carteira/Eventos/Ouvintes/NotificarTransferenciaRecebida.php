<?php

namespace YouPay\Operacao\Dominio\Carteira\Eventos\Ouvintes;

use YouPay\Operacao\Dominio\Carteira\Eventos\Emitidos\TransferenciaEfetivada;
use YouPay\Operacao\Dominio\Conta\Conta;
use YouPay\Operacao\Servicos\Notificador;
use YouPay\Shared\Dominio\EventoInterface;
use YouPay\Shared\Dominio\OuvinteEvento;

class NotificarTransferenciaRecebida extends OuvinteEvento
{
    private Notificador $notificador;

    public function __construct(Notificador $servicoNotificador)
    {
        $this->notificador = $servicoNotificador;
    }

    public function sabeProcessar(EventoInterface $evento): bool
    {
        return $evento instanceof TransferenciaEfetivada;
    }

    public function reagir(EventoInterface $evento): void
    {
        /** @var Conta $contaOrigem */
        $contaOrigem = $evento->getContaOrigem();
        $mensagem = sprintf(
            'Você recebeu uma transferência de %s no valor de R$ %s',
            $contaOrigem->getTitular(),
            $evento->getValor()
        );
        $this->notificador->notificar($mensagem);
    }
}
