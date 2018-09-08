<?php

namespace App\Tests\Controller;

use App\Service\UserService;
use App\Tests\Classes\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @covers \App\Controller\LoginRESTController
 */
class LoginRESTControllerTest extends WebTestCase
{

    public function test_login_v1()
    {
        $client = $this->createUnauthorizedClient();
        $userService = $client->getContainer()->get(UserService::class);

        $user = $userService->create([
            'name' => md5(uniqid()),
            'email' => md5(uniqid()) . '@mail.com',
            'password' => '12345',
        ]);

        $client->request('POST', "/api/v1/login", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ], json_encode([
            'login' => $user->getUsername(),
            'password' => '12345'
        ]));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['user']), 'Missing user');
        $this->assertTrue(isset($content['token']), 'Missing token');
    }
}