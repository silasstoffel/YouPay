<?php

namespace YouPay\Shared\Dominio;

abstract class OuvinteEvento
{
    /**
     * Processa o evento recebido.
     *
     * @param  EventoInterface $evento
     * @return void
     */
    public function processar(EventoInterface $evento)
    {
        if ($this->sabeProcessar($evento)) {
            $this->reagir($evento);
        }
    }

    /**
     * Definição se ouvinte sabe processar o evento.
     *
     * @param  EventoInterface $evento evento
     * @return bool
     */
    abstract public function sabeProcessar(EventoInterface $evento): bool;

    /**
     * Como deve reagir ao evento recebido.
     *
     * @param  EventoInterface $evento evento
     * @return void
     */
    abstract public function reagir(EventoInterface $evento): void;
}
