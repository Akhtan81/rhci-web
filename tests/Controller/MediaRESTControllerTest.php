<?php

namespace App\Tests\Controller;

use App\Tests\Classes\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @covers \App\Controller\MediaRESTController
 */
class MediaRESTControllerTest extends WebTestCase
{

    public function test_post_not_authorized()
    {
        $client = $this->createUnauthorizedClient();

        $path = '/tmp/MediaRESTControllerTest.txt';

        file_put_contents($path, md5(uniqid()));

        $client->request('POST', "/api/v1/media", [], [
            'file' => new UploadedFile($path, 'MediaRESTControllerTest.txt', 'text/plain', UPLOAD_ERR_OK, true)
        ], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function test_post()
    {
        $client = $this->createUnauthorizedClient();
        $accessToken = $this->getUserAccessToken();

        $path = '/tmp/MediaRESTControllerTest.txt';

        file_put_contents($path, md5(uniqid()));

        $client->request('POST', "/api/v1/media", [], [
            'file' => new UploadedFile($path, 'MediaRESTControllerTest.txt', 'text/plain', UPLOAD_ERR_OK, true)
        ], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'HTTP_Authorization' => $accessToken
        ]);

        $response = $client->getResponse();

        $this->assertTrue($response->isSuccessful());

        $content = json_decode($response->getContent(), true);
        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['hash']), 'Missing hash');
        $this->assertTrue(isset($content['url']), 'Missing url');
    }
}