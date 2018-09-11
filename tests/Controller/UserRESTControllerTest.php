<?php

namespace App\Tests\Controller;

use App\Service\MediaService;
use App\Tests\Classes\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @covers \App\Controller\UserRESTController
 */
class UserRESTControllerTest extends WebTestCase
{

    public function test_post_signup()
    {
        $client = $this->createUnauthorizedClient();
        $mediaService = $client->getContainer()->get(MediaService::class);

        $path = '/tmp/UserRESTControllerTest.txt';

        file_put_contents($path, md5(uniqid()));

        $file = new UploadedFile($path, 'UserRESTControllerTest.txt', 'text/plain', UPLOAD_ERR_OK, true);

        $media = $mediaService->create($file);

        $client->request('POST', "/api/v1/signup", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ], json_encode([
            'name' => md5(uniqid()),
            'email' => md5(uniqid()) . '@mail.com',
            'password' => '12345',
            'avatar' => $media->getId(),
        ]));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['isActive']), 'Missing isActive');
        $this->assertTrue($content['isActive']);
    }

    public function test_post_signup_with_credit_card()
    {
        $client = $this->createUnauthorizedClient();

        $primaryCard = md5(uniqid());

        $client->request('POST', "/api/v1/signup", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ], json_encode([
            'name' => md5(uniqid()),
            'email' => md5(uniqid()) . '@mail.com',
            'password' => '12345',
            'creditCards' => [
                [
                    'token' => $primaryCard,
                    'name' => '4242',
                    'isPrimary' => true
                ],
                [
                    'token' => md5(uniqid()),
                    'name' => '4243',
                ]
            ]
        ]));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['isActive']), 'Missing isActive');
        $this->assertTrue($content['isActive']);

        $this->assertTrue(isset($content['creditCards']), 'Missing creditCards');
        $this->assertEquals(2, count($content['creditCards']));

        $this->assertTrue(isset($content['primaryCreditCard']), 'Missing primaryCreditCard');
        $this->assertTrue(isset($content['primaryCreditCard']['token']), 'Missing primaryCreditCard.token');
        $this->assertEquals($primaryCard, $content['primaryCreditCard']['token']);
    }

    public function test_put_me()
    {
        $client = $this->createUnauthorizedClient();
        $accessToken = $this->getUserAccessToken();

        $client->request('PUT', "/api/v1/me", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'HTTP_Authorization' => $accessToken
        ], json_encode([
            'name' => md5(uniqid()),
        ]));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
    }

    public function test_get_me()
    {
        $client = $this->createUnauthorizedClient();
        $accessToken = $this->getUserAccessToken();

        $client->request('GET', "/api/v1/me", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'HTTP_Authorization' => $accessToken
        ]);

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
    }

    public function test_password()
    {
        $client = $this->createUnauthorizedClient();
        $accessToken = $this->getUserAccessToken();

        $client->request('PUT', "/api/v1/me", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'HTTP_Authorization' => $accessToken
        ], json_encode([
            'password' => '12345',
            'currentPassword' => '12345',
        ]));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
    }
}