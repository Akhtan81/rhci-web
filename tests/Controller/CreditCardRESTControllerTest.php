<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Service\CreditCardService;
use App\Tests\Classes\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers \App\Controller\CreditCardRESTController
 */
class CreditCardRESTControllerTest extends WebTestCase
{

    public function test_post()
    {
        $client = $this->createUnauthorizedClient();
        $accessToken = $this->getUserAccessToken();

        $client->request('POST', "/api/v1/me/credit-cards", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'HTTP_Authorization' => $accessToken
        ], json_encode([
            'type' => 'Visa',
            'isPrimary' => true,
            'token' => md5(uniqid()),
            'name' => '12345',
        ]));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['token']), 'Missing token');
    }

    public function test_put()
    {
        $client = $this->createUnauthorizedClient();
        $creditCardService = $client->getContainer()->get(CreditCardService::class);
        $em = $client->getContainer()->get('doctrine')->getManager();

        /** @var User $user */
        $user = $em->getRepository(User::class)->findOneBy([]);
        if (!$user) {
            $this->fail("User was not found");
        }

        $card = $creditCardService->create($user, [
            'type' => 'Visa',
            'isPrimary' => true,
            'token' => md5(uniqid()),
            'name' => '12345',
        ]);

        $accessToken = $user->getAccessToken();

        $client->request('PUT', "/api/v1/me/credit-cards/" . $card->getId(), [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'HTTP_Authorization' => $accessToken
        ], json_encode([
            'name' => '54321',
        ]));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['token']), 'Missing token');
    }

    public function test_delete()
    {
        $client = $this->createUnauthorizedClient();
        $creditCardService = $client->getContainer()->get(CreditCardService::class);
        $em = $client->getContainer()->get('doctrine')->getManager();

        /** @var User $user */
        $user = $em->getRepository(User::class)->findOneBy([]);
        if (!$user) {
            $this->fail("User was not found");
        }

        $card = $creditCardService->create($user, [
            'type' => 'Visa',
            'isPrimary' => true,
            'token' => md5(uniqid()),
            'name' => '12345',
        ]);

        $accessToken = $user->getAccessToken();

        $client->request('DELETE', "/api/v1/me/credit-cards/" . $card->getId(), [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'HTTP_Authorization' => $accessToken
        ]);

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function test_get()
    {
        $client = $this->createUnauthorizedClient();

        $accessToken = $this->getUserAccessToken();

        $client->request('GET', "/api/v1/me/credit-cards", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'HTTP_Authorization' => $accessToken
        ]);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['count']), 'Missing count');
        $this->assertTrue(isset($content['items']), 'Missing items');
    }

    public function test_get_unauthorized()
    {
        $client = $this->createUnauthorizedClient();

        $client->request('GET', "/api/v1/me/credit-cards", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function test_post_unauthorized()
    {
        $client = $this->createUnauthorizedClient();

        $client->request('POST', "/api/v1/me/credit-cards", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ], json_encode([]));

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function test_put_unauthorized()
    {
        $client = $this->createUnauthorizedClient();

        $client->request('PUT', "/api/v1/me/credit-cards/1", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ], json_encode([]));

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function test_delete_unauthorized()
    {
        $client = $this->createUnauthorizedClient();

        $client->request('DELETE', "/api/v1/me/credit-cards/1", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }


}