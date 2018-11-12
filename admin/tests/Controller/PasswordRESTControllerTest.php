<?php

namespace App\Tests\Controller;

use App\Service\UserService;
use App\Tests\Classes\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @covers \App\Controller\PasswordRESTController
 */
class PasswordRESTControllerTest extends WebTestCase
{

    /**
     * @small
     */
    public function test_post_reset_password()
    {
        $client = $this->createUnauthorizedClient();

        $client->request('POST', "/api/v1/users/reset-password", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ], json_encode([
            'login' => 'user',
        ]));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertFalse(isset($content['passwordToken']), 'Found passwordToken');
    }

    /**
     * @medium
     */
    public function test_put_set_password()
    {
        $client = $this->createUnauthorizedClient();
        $userService = $client->getContainer()->get(UserService::class);

        $user = $userService->findOneByFilter([
            'login' => 'user'
        ]);
        if (!$user) {
            $this->fail('User not found');
        }

        $client->request('POST', "/api/v1/users/reset-password", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ], json_encode([
            'login' => $user->getEmail(),
        ]));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertFalse(isset($content['passwordToken']), 'Found passwordToken');

        $user = $userService->findOneByFilter([
            'login' => 'user'
        ]);
        if (!$user) {
            $this->fail('User not found');
        }

        $token = $user->getPasswordToken();
        $this->assertNotEmpty($token, 'Missing passwordToken');

        $newPass = md5(uniqid());

        $client = $this->createUnauthorizedClient();

        $client->request('PUT', "/api/v1/users/" . $token . "/password", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ], json_encode([
            'password' => $newPass,
        ]));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());

        $client = $this->createUnauthorizedClient();

        $client->request('POST', "/api/v1/login", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ], json_encode([
            'login' => $user->getEmail(),
            'password' => $newPass,
        ]));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
    }
}
