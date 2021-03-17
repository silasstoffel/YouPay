<?php

/** @var \Laravel\Lumen\Routing\Router $router */

\YouPay\App::bootstrap();

$router->get('/', function() {
    return redirect('/api-docs/index.html');
});

$router->post('/auth', 'AuthController@store');

$router->post('/contas', 'ContaController@store');

$v1 = ['prefix' => '/v1', 'middleware' => 'auth'];

$router->group($v1, function () use ($router) {
    // Efetivar transferência
    $router->post('/operacoes/transferir', 'TransferenciaController@store');
});
