<?php

namespace YouPay;

use YouPay\Operacao\Dominio\Carteira\Eventos\Ouvintes\CriarSaldoInicial;
use YouPay\Operacao\Infra\Carteira\RepositorioSaldo;
use YouPay\Shared\Dominio\PublicadorEvento;

class App
{
    private static PublicadorEvento $publicadorEventos;

    public static function bootstrap()
    {
        self::inicializarPublicadorEventos();
    }

    public static function getPublicadorEventos(): PublicadorEvento
    {
        return self::$publicadorEventos;
    }

    private static function inicializarPublicadorEventos()
    {
        self::$publicadorEventos = new PublicadorEvento();

        // Quando uma conta for criada deve criar registro de saldo na carteira
        self::$publicadorEventos->adicionarOuvinte(
            new CriarSaldoInicial(new RepositorioSaldo())
        );
    }

}
