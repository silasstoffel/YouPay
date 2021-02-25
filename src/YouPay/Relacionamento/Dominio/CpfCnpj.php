<?php

namespace YouPay\Relacionamento\Dominio;


use Bissolli\ValidadorCpfCnpj\Documento;
use DomainException;

class CpfCnpj
{
    private ?string $numero = null;

    public function __construct(string $numero)
    {
        $this->setNumero($numero);
    }

    public function __toString()
    {
        return $this->numero;
    }


    /**
     * Obtem numero do CPF ou CNPJ
     */
    public function getNumero(): string
    {
        return $this->numero;
    }

    /**
     * Set the value of numero
     *
     * @return  void
     */
    private function setNumero(string $numero):void
    {
        $num = str_replace(['.', '-', ' ', '/'], '', $numero);

        $doc = new Documento($num);

        if (!$doc->isValid()) {
            throw new DomainException('CPF ou CNPJ inválido.', 400);
        }

        $this->numero = $num;
    }

}
