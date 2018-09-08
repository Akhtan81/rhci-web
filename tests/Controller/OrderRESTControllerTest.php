<?php

namespace App\Tests\Controller;

use App\Entity\OrderRepeat;
use App\Service\CategoryService;
use App\Service\MediaService;
use App\Tests\Classes\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @covers \App\Controller\OrderRESTController
 */
class OrderRESTControllerTest extends WebTestCase
{

    public function test_post()
    {
        $client = $this->createUnauthorizedClient();
        $mediaService = $client->getContainer()->get(MediaService::class);
        $categoryService = $client->getContainer()->get(CategoryService::class);

        $path = '/tmp/OrderRESTControllerTest.txt';
        file_put_contents($path, md5(uniqid()));

        $file = new UploadedFile($path, 'OrderRESTControllerTest.txt', 'text/plain', UPLOAD_ERR_OK, true);

        $media = $mediaService->create($file);

        $categories = $categoryService->findByFilter([
            'locale' => 'en',
            'isSelectable' => true,
            'hasPrice' => true,
        ], 1, 2);

        $repeatables = [null, OrderRepeat::WEEK, OrderRepeat::MONTH];

        $content = [
            'location' => [
                'lat' => 12.12345,
                'lng' => 21.12345,
                'address' => md5(uniqid()),
                'postalCode' => '00000'
            ],
            'scheduledAt' => date('Y-m-d H:i'),
            'repeatable' => $repeatables[array_rand($repeatables)],
            'items' => [
                [
                    'category' => $categories[0]->getId(),
                    'quantity' => 1
                ],
                [
                    'category' => $categories[1]->getId(),
                    'quantity' => 10
                ]
            ],
            'message' => [
                'text' => md5(uniqid()),
                'files' => [
                    $media->getId()
                ]
            ]
        ];

        $accessToken = $this->getUserAccessToken();

        $client->request('POST', "/api/v1/orders", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'HTTP_Authorization' => $accessToken
        ], json_encode($content));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());
    }
}