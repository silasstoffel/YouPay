<?php

/** @var \Laravel\Lumen\Routing\Router $router */


$router->post('/contas', 'ContaController@store');

$router->post('/auth', 'AuthController@store');
