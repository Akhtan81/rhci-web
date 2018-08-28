<?php

namespace App\Tests\Controller;

use App\Tests\Classes\WebTestCase;

/**
 * @covers \App\Controller\AchievementController
 */
class AchievementControllerTest extends WebTestCase
{

    public function test_IndexPageNotOpened_if_NotAuthorized()
    {
        $client = $this->createUnauthorizedClient();

        $client->request('GET', "/achievements");

        $response = $client->getResponse();

        $this->assertTrue($this->isRedirect($response, '/login'));
    }

    public function test_IndexPageIsOpened_if_Authorized()
    {
        $client = $this->createAuthorizedClient();

        $client->request('GET', "/achievements");

        $response = $client->getResponse();

        $this->assertTrue($response->isSuccessful());
    }
}