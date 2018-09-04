<?php

namespace App\Tests\Controller;

use App\Service\UserService;
use App\Tests\Classes\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @covers \App\Controller\LocationRESTController
 */
class LocationRESTControllerTest extends WebTestCase
{

    public function test_delete()
    {
        $client = $this->createAuthorizedUser();
        $userService = $client->getContainer()->get(UserService::class);

        $user = $userService->create([
            'name' => md5(uniqid()),
            'email' => md5(uniqid()) . '@mail.com',
            'password' => '12345',
            'location' => [
                'lat' => 12.12345,
                'lng' => 21.12345,
                'address' => md5(uniqid()),
            ]
        ]);

        $client->request('DELETE', "/api/v1/users/" . $user->getId() . '/locations/' . $user->getLocation()->getId());

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function test_delete_me()
    {
        $client = $this->createAuthorizedUser();
        $userService = $client->getContainer()->get(UserService::class);

        $user = $userService->create([
            'name' => md5(uniqid()),
            'email' => md5(uniqid()) . '@mail.com',
            'password' => '12345',
            'location' => [
                'lat' => 12.12345,
                'lng' => 21.12345,
                'address' => md5(uniqid()),
            ]
        ]);

        $client = $this->createAuthorizedClient($user->getUsername());

        $client->request('DELETE', "/api/v1/me/locations/" . $user->getLocation()->getId());

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_NO_CONTENT, $response->getStatusCode());
    }
}
