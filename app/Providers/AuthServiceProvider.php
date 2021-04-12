<?php

namespace App\Providers;

use Exception;
use Illuminate\Auth\GenericUser;
use Illuminate\Support\ServiceProvider;
use YouPay\Operacao\Infra\Conta\GerenciadorToken;
use YouPay\Operacao\Infra\Conta\RepositorioConta;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return GenericUser|null
     */
    public function boot()
    {
        $this->app['auth']->viaRequest('api', function ($request) {
            if (!$request->hasHeader('Authorization')) {
                return null;
            }
            $user = null;
            try {
                $authorization = $request->header('Authorization', null);
                $jwt           = str_replace('Bearer ', '', $authorization);
                $token = new GerenciadorToken(env('JWT_SECRET'));
                $decoded       = $token->decodificar($jwt);
                if (!isset($decoded->id)) {
                    return null;
                }
                $repositorioConta = new RepositorioConta();
                $conta            = $repositorioConta->buscarId($decoded->id);
                if (!is_null($conta)) {
                    $user = new GenericUser([
                        'id'    => $conta->getId(),
                        'name'  => $conta->getTitular(),
                        'email' => $conta->getEmail(),
                    ]);
                }
            } catch (Exception $exc) {
                return null;
            }
            return $user;
        });
    }
}
