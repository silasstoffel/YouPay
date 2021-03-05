<?php

/** @var \Laravel\Lumen\Routing\Router $router */


$router->post('/auth', 'AuthController@store');

$router->post('/v1/contas', 'ContaController@store');
