<?php

namespace YouPay\Relacionamento\Dominio;

class Email
{
    private string $endereco;

    public function __construct(string $endereco)
    {
        if (filter_var($endereco, FILTER_VALIDATE_EMAIL) === false) {
            throw new \DomainException('Endereço de e-mail inválido', 400);
        }

        $this->endereco = $endereco;
    }

    public function __toString(): string
    {
        return $this->endereco;
    }
}
