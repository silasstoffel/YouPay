<?php

use YouPay\Operacao\Dominio\Email;

class EmailTest extends TestCase
{
    public function testCriarInstanciaComEmailValido()
    {
        $email = new Email('cliente@youpay.com.br');
        $this->assertEquals('cliente@youpay.com.br', $email);
    }

    public function testNaoPodeCriarInstanciaComEmailInvalido()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Endereço de e-mail inválido.');
        new Email('mail.youpay.com');
    }
}
