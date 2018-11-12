<?php

namespace App\Tests\Controller;

use App\Service\CreditCardService;
use App\Service\UserService;
use App\Tests\Classes\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers \App\Controller\CreditCardRESTController
 */
class CreditCardRESTControllerTest extends WebTestCase
{

    /**
     * @small
     */
    public function test_post()
    {
        $client = $this->createUnauthorizedClient();

        $userService = $client->getContainer()->get(UserService::class);

        $user = $userService->create([
            'name' => md5(uniqid()),
            'email' => md5(uniqid()),
            'phone' => md5(uniqid()),
            'password' => '12345'
        ]);

        $accessToken = $user->getAccessToken();

        $client->request('POST', "/api/v1/me/credit-cards", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'HTTP_Authorization' => $accessToken
        ], json_encode([
            'type' => 'Visa',
            'token' => md5(uniqid()),
            'lastFour' => '4242',
        ]));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['token']), 'Missing token');
        $this->assertTrue(isset($content['isPrimary']), 'Missing isPrimary');
        $this->assertTrue($content['isPrimary'], 'Invalid isPrimary');
    }

    /**
     * @small
     */
    public function test_put()
    {
        $client = $this->createUnauthorizedClient();
        $creditCardService = $client->getContainer()->get(CreditCardService::class);
        $userService = $client->getContainer()->get(UserService::class);

        $user = $userService->create([
            'name' => md5(uniqid()),
            'email' => md5(uniqid()),
            'phone' => md5(uniqid()),
            'password' => '12345'
        ]);

        $card = $creditCardService->create($user, [
            'type' => 'Visa',
            'isPrimary' => true,
            'token' => md5(uniqid()),
            'lastFour' => '4242',
        ]);

        $accessToken = $user->getAccessToken();

        $client->request('PUT', "/api/v1/me/credit-cards/" . $card->getId(), [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'HTTP_Authorization' => $accessToken
        ], json_encode([
            'lastFour' => '4243',
        ]));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['token']), 'Missing token');
    }

    /**
     * @small
     */
    public function test_delete()
    {
        $client = $this->createUnauthorizedClient();
        $creditCardService = $client->getContainer()->get(CreditCardService::class);
        $userService = $client->getContainer()->get(UserService::class);

        $user = $userService->create([
            'name' => md5(uniqid()),
            'email' => md5(uniqid()),
            'phone' => md5(uniqid()),
            'password' => '12345'
        ]);

        $card = $creditCardService->create($user, [
            'type' => 'Visa',
            'isPrimary' => true,
            'token' => md5(uniqid()),
            'lastFour' => '4242',
        ]);

        $accessToken = $user->getAccessToken();

        $client->request('DELETE', "/api/v1/me/credit-cards/" . $card->getId(), [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'HTTP_Authorization' => $accessToken
        ]);

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    /**
     * @small
     */
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

    /**
     * @small
     */
    public function test_get_unauthorized()
    {
        $client = $this->createUnauthorizedClient();

        $client->request('GET', "/api/v1/me/credit-cards", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /**
     * @small
     */
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

    /**
     * @small
     */
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

    /**
     * @small
     */
    public function test_delete_unauthorized()
    {
        $client = $this->createUnauthorizedClient();

        $client->request('DELETE', "/api/v1/me/credit-cards/1", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /**
     * @medium
     */
    public function test_if_not_primary_credit_card_is_removed_then_primary_card_is_not_changed()
    {
        $client = $this->createUnauthorizedClient();
        $userService = $client->getContainer()->get(UserService::class);

        $card1 = md5(uniqid());
        $card2 = md5(uniqid());

        $user = $userService->create([
            'name' => md5(uniqid()),
            'email' => md5(uniqid()),
            'phone' => md5(uniqid()),
            'password' => '12345',
            'creditCards' => [
                [
                    'token' => $card1,
                    'isPrimary' => true,
                    'lastFour' => '4242'
                ],
                [
                    'token' => $card2,
                    'lastFour' => '4243'
                ]
            ]
        ]);

        $accessToken = $user->getAccessToken();

        $client->request('GET', "/api/v1/me", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'HTTP_Authorization' => $accessToken
        ]);

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');

        $this->assertTrue(isset($content['primaryCreditCard']), 'Missing primaryCreditCard');
        $this->assertEquals($card1, $content['primaryCreditCard']['token'], 'Invalid primaryCreditCard.token');

        $this->assertTrue(isset($content['creditCards']), 'Missing creditCards');
        $this->assertEquals(2, count($content['creditCards']), 'Invalid creditCards');

        foreach ($content['creditCards'] as $creditCard) {
            $this->assertTrue(isset($creditCard['isPrimary']), 'Missing creditCards.isPrimary');

            switch ($creditCard['token']) {
                case $card1:
                    $this->assertTrue($creditCard['isPrimary'], 'Invalid creditCards.isPrimary');
                    break;
                case $card2:
                    $this->assertFalse($creditCard['isPrimary'], 'Invalid creditCards.isPrimary');
                    break;
                default:
                    $this->fail('Unknown token ' . $creditCard['token']);
            }
        }


        $client->request('DELETE', "/api/v1/me/credit-cards/" . $content['creditCards'][1]['id'], [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'HTTP_Authorization' => $accessToken
        ]);

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_NO_CONTENT, $response->getStatusCode());


        $client->request('GET', "/api/v1/me", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'HTTP_Authorization' => $accessToken
        ]);

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');

        $this->assertTrue(isset($content['primaryCreditCard']), 'Missing primaryCreditCard');
        $this->assertEquals($card1, $content['primaryCreditCard']['token'], 'Invalid primaryCreditCard.token');

        $this->assertTrue(isset($content['creditCards']), 'Missing creditCards');
        $this->assertEquals(1, count($content['creditCards']), 'Invalid creditCards');

        foreach ($content['creditCards'] as $creditCard) {
            switch ($creditCard['token']) {
                case $card1:
                    $this->assertTrue($creditCard['isPrimary'], 'Invalid creditCards.isPrimary');
                    break;
                default:
                    $this->fail('Unknown token ' . $creditCard['token']);
            }
        }
    }

    /**
     * @small
     */
    public function test_if_new_primary_credit_card_is_created_then_it_is_set_as_primary()
    {
        $client = $this->createUnauthorizedClient();
        $userService = $client->getContainer()->get(UserService::class);

        $card1 = md5(uniqid());

        $user = $userService->create([
            'name' => md5(uniqid()),
            'email' => md5(uniqid()),
            'phone' => md5(uniqid()),
            'password' => '12345'
        ]);

        $accessToken = $user->getAccessToken();

        $client->request('GET', "/api/v1/me", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'HTTP_Authorization' => $accessToken
        ]);

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertFalse(isset($content['primaryCreditCard']), 'Missing primaryCreditCard');


        $client->request('POST', "/api/v1/me/credit-cards", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'HTTP_Content-Type' => 'application/json',
            'HTTP_Authorization' => $accessToken
        ], json_encode([
            'token' => $card1,
            'lastFour' => '4242'
        ]));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());



        $client->request('GET', "/api/v1/me", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'HTTP_Authorization' => $accessToken
        ]);

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');

        $this->assertTrue(isset($content['primaryCreditCard']), 'Missing primaryCreditCard');
        $this->assertEquals($card1, $content['primaryCreditCard']['token'], 'Invalid primaryCreditCard.token');

        $this->assertTrue(isset($content['creditCards']), 'Missing creditCards');
        $this->assertEquals(1, count($content['creditCards']), 'Invalid creditCards');

        foreach ($content['creditCards'] as $creditCard) {
            switch ($creditCard['token']) {
                case $card1:
                    $this->assertTrue($creditCard['isPrimary'], 'Invalid creditCards.isPrimary');
                    break;
                default:
                    $this->fail('Unknown token ' . $creditCard['token']);
            }
        }
    }

}