<?php

namespace App\Tests\Controller;

use App\Tests\Classes\WebTestCase;

/**
 * @covers \App\Controller\CategoryRESTController
 */
class CategoryRESTControllerTest extends WebTestCase
{

    public function test_get_if_not_authorized()
    {
        $client = $this->createUnauthorizedClient();

        $client->request('GET', "/api/v1/categories", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response = $client->getResponse();

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function test_get_if_authorized()
    {
        $client = $this->createAuthorizedClient('content-manager');

        $client->request('GET', "/api/v1/categories", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response = $client->getResponse();

        $this->assertTrue($response->isSuccessful());

        $content = json_decode($response->getContent(), true);
        $this->assertTrue(isset($content['items']), 'Missing items');
        $this->assertTrue(isset($content['count']), 'Missing count');
        $this->assertTrue(isset($content['page']), 'Missing page');
        $this->assertTrue(isset($content['limit']), 'Missing limit');
        $this->assertTrue(isset($content['total']), 'Missing total');
    }
}