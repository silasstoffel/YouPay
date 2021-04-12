<?php

use Laravel\Lumen\Application;
use Laravel\Lumen\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{

    protected $_login    = '';
    protected $_password = '';

    /**
     * Creates the application.
     *
     * @return Application
     */
    public function createApplication(): Application
    {
        return require __DIR__ . '/../bootstrap/app.php';
    }

    protected function createToken(): array
    {
        $data     = ['login' => $this->_login, 'password' => $this->_password];
        $response = $this->json('POST', '/auth', $data);

        $body  = $response->response->getOriginalContent();
        $token = $body['token'] ?? null;

        return ['Authorization' => 'Bearer ' . $token];
    }
}
