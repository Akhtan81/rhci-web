<?php

namespace App\Tests\Controller;

use App\Tests\Classes\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @covers \App\Controller\MediaRESTController
 */
class MediaRESTControllerTest extends WebTestCase
{

    public function test_post()
    {
        $client = $this->createUnauthorizedClient();

        $path = '/tmp/MediaRESTControllerTest.txt';

        file_put_contents($path, md5(uniqid()));

        $client->request('POST', "/api/v1/media", [], [
            'file' => new UploadedFile($path, 'MediaRESTControllerTest.txt', 'text/plain', UPLOAD_ERR_OK, true)
        ]);

        $response = $client->getResponse();

        $this->assertTrue($response->isSuccessful());

        $content = json_decode($response->getContent(), true);
        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['hash']), 'Missing hash');
        $this->assertTrue(isset($content['url']), 'Missing url');
    }
}