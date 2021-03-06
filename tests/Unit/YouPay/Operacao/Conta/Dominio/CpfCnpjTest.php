<?php


use YouPay\Operacao\Dominio\CpfCnpj;

class CpfCnpjTest extends TestCase
{
    public function testCriarInstanciaComCpfValido()
    {
        $numero = '866.270.620-70';
        $cpf = new CpfCnpj($numero);
        $this->assertEquals('86627062070', $cpf);
    }

    public function testNaoPodeCriarInstanciaComCpfInvalido()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('CPF ou CNPJ inválido.');
        new CpfCnpj('86627062000');
    }

    public function testCriarInstanciaComCnpjValido()
    {
        $numero = '10853248000159';
        $cnpj = new CpfCnpj($numero);
        $this->assertEquals('10853248000159', $cnpj);
    }

    public function testNaoPodeCriarInstanciaComCnpjInvalido()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('CPF ou CNPJ inválido.');
        new CpfCnpj('10843248000159');
    }

}
