<?php

namespace App\Tests\Controller;

use App\Entity\OrderRepeat;
use App\Service\MediaService;
use App\Service\PartnerCategoryService;
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
        $partnerCategoryService = $client->getContainer()->get(PartnerCategoryService::class);

        $path = '/tmp/OrderRESTControllerTest.txt';
        file_put_contents($path, md5(uniqid()));

        $file = new UploadedFile($path, 'OrderRESTControllerTest.txt', 'text/plain', UPLOAD_ERR_OK, true);

        $media = $mediaService->create($file);

        $categories = $partnerCategoryService->findByFilter([
            'isSelectable' => true,
            'hasPrice' => true,
        ], 1, 2);
        if (count($categories) !== 2) {
            $this->fail('Partner categories not found');
        }

        $repeatables = [null, OrderRepeat::WEEK, OrderRepeat::MONTH];

        $category1 = $categories[0]->getCategory()->getId();
        $price1 = $categories[0]->getPrice();

        $category2 = $categories[1]->getCategory()->getId();
        $price2 = $categories[1]->getPrice();
        $priceTotal = $price1 + ($price2 * 10);

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
                    'category' => $category1,
                    'quantity' => 1
                ],
                [
                    'category' => $category2,
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

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['price']), 'Missing price');
        $this->assertTrue(isset($content['location']['id']), 'Missing location.id');
        $this->assertTrue(isset($content['district']['id']), 'Missing district.id');
        $this->assertTrue(isset($content['partner']['id']), 'Missing partner.id');
        $this->assertTrue(isset($content['items']), 'Missing items');
        $this->assertTrue(isset($content['price']), 'Missing price');

        foreach ($content['items'] as $item) {
            $this->assertTrue(isset($item['category']['id']), 'Missing item.category.id');
            $this->assertTrue(isset($item['partnerCategory']['id']), 'Missing item.partnerCategory.id');
            $this->assertTrue(isset($item['price']), 'Missing item.price');

            switch ($item['category']['id']) {
                case $category1:
                    $this->assertEquals($price1, $item['price'], 'Invalid item.price');
                    break;
                case $category2:
                    $this->assertEquals($price2, $item['price'], 'Invalid item.price');
                    break;
                default:
                    $this->fail('Unknown item.category.id');
            }
        }

        $this->assertEquals($priceTotal, $content['price'], 'Invalid price');
    }
}