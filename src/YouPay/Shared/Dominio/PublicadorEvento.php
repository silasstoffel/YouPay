<?php

namespace YouPay\Shared\Dominio;

class PublicadorEvento
{

    /**
     * Ouvintes
     *
     * @var OuvinteEvento[]
     */
    private $ouvintes = [];

    /**
     * __construct
     *
     * @param  OuvinteEvento[] $ouvintes array de ouvintes
     * @return void
     */
    public function __construct(array $ouvintes = [])
    {
        $this->ouvintes = $ouvintes;
    }

    /**
     * Publicação do evento ao todos os ouvintes.
     *
     * @param  mixed $evento
     * @return void
     */
    public function publicar(EventoInterface $evento)
    {
        /** @var OuvinteEvento $ouvinte */
        foreach ($this->ouvintes as $ouvinte) {
            $ouvinte->processar($evento);
        }
    }

    /**
     * Adiciona Ouvinte
     *
     * @param  OuvinteEvento $ouvinte ouvinte que herde de OuvinteEvento
     * @return void
     */
    public function adicionarOuvinte(OuvinteEvento $ouvinte)
    {
        $this->ouvintes[] = $ouvinte;
    }

}
