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

    public function test_post()
    {
        $client = $this->createUnauthorizedClient();
        $mediaService = $client->getContainer()->get(MediaService::class);

        $path = '/tmp/UserRESTControllerTest.txt';

        file_put_contents($path, md5(uniqid()));

        $file = new UploadedFile($path, 'UserRESTControllerTest.txt', 'text/plain', UPLOAD_ERR_OK, true);

        $media = $mediaService->create($file);

        $client->request('POST', "/api/v1/users", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ], json_encode([
            'name' => md5(uniqid()),
            'email' => md5(uniqid()) . '@mail.com',
            'password' => '12345',
            'avatar' => $media->getId()
        ]));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['isActive']), 'Missing isActive');
        $this->assertFalse($content['isActive']);
    }
}