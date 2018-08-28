<?php

namespace App\Tests\Controller;

use App\Tests\Classes\WebTestCase;

/**
 * @covers \App\Controller\LoginController
 */
class LoginControllerTest extends WebTestCase
{

    public function testLoginPageOpens()
    {
        $client = $this->createUnauthorizedClient();

        $client->request('GET', "/login");

        $response = $client->getResponse();

        $this->assertTrue($response->isSuccessful());
    }
}