<?php

namespace App\Tests\Controller;

use App\Service\UserService;
use App\Tests\Classes\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @covers \App\Controller\IndexController
 */
class IndexControllerTest extends WebTestCase
{

    public function pages()
    {
        return [
            ['/login'],
            ['/register'],
            ['/introduction'],
            ['/privacy'],
            ['/terms'],
            ['/reset-password'],
        ];
    }

    /**
     * @small
     *
     * @dataProvider pages
     * @param $page
     */
    public function test_page_opens($page)
    {
        $client = $this->createUnauthorizedClient();

        $client->request('GET', $page);

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
    }
}