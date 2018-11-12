<?php

namespace App\Tests\Controller;

use App\Service\LocationService;
use App\Service\UserLocationService;
use App\Service\UserService;
use App\Tests\Classes\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @covers \App\Controller\UserLocationRESTController
 */
class UserLocationRESTControllerTest extends WebTestCase
{

    /**
     * @small
     */
    public function test_delete()
    {
        $client = $this->createUnauthorizedClient();

        $userService = $client->getContainer()->get(UserService::class);
        $locationService = $client->getContainer()->get(LocationService::class);
        $userLocationService = $client->getContainer()->get(UserLocationService::class);

        $user = $userService->create([
            'name' => md5(uniqid()),
            'email' => md5(uniqid()) . '@mail.com',
            'password' => '12345',
        ]);

        $location = $locationService->create([
            'lat' => 12.12345,
            'lng' => 21.12345,
            'address' => md5(uniqid()),
            'postalCode' => '00000'
        ]);

        $userLocation = $userLocationService->create($user, $location);

        $accessToken = $user->getAccessToken();

        $client->request('DELETE', "/api/v1/users/" . $user->getId() . '/locations/' . $userLocation->getId(), [], [], [
            'HTTP_Authorization' => $accessToken
        ]);

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    /**
     * @small
     */
    public function test_delete_me()
    {
        $client = $this->createUnauthorizedClient();

        $userService = $client->getContainer()->get(UserService::class);
        $locationService = $client->getContainer()->get(LocationService::class);
        $userLocationService = $client->getContainer()->get(UserLocationService::class);

        $user = $userService->create([
            'name' => md5(uniqid()),
            'email' => md5(uniqid()) . '@mail.com',
            'password' => '12345',
        ]);

        $location = $locationService->create([
            'lat' => 12.12345,
            'lng' => 21.12345,
            'address' => md5(uniqid()),
            'postalCode' => '00000'
        ]);

        $userLocation = $userLocationService->create($user, $location);

        $accessToken = $user->getAccessToken();

        $client->request('DELETE', "/api/v1/me/locations/" . $userLocation->getId(), [], [], [
            'HTTP_Authorization' => $accessToken
        ]);

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_NO_CONTENT, $response->getStatusCode());
    }
}
