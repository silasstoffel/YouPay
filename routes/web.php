<?php

/** @var \Laravel\Lumen\Routing\Router $router */


\YouPay\App::bootstrap();

$router->post('/auth', 'AuthController@store');

$router->post('/v1/contas', 'ContaController@store');

$router->post('/v1/operacoes/transferir', 'TransferenciaController@store');
