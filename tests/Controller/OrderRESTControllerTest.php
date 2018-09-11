<?php

namespace App\Tests\Controller;

use App\Entity\OrderRepeat;
use App\Entity\OrderStatus;
use App\Entity\PartnerPostalCode;
use App\Service\MediaService;
use App\Service\OrderService;
use App\Service\PartnerCategoryService;
use App\Service\UserService;
use App\Tests\Classes\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @covers \App\Controller\OrderRESTController
 */
class OrderRESTControllerTest extends WebTestCase
{

    public function test_gets_v1_unauthorized()
    {
        $client = $this->createUnauthorizedClient();

        $client->request('GET', "/api/v1/orders", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function test_gets_v2_unauthorized()
    {
        $client = $this->createUnauthorizedClient();

        $client->request('GET', "/api/v2/orders", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function test_gets_v2_forbidden_user()
    {
        $client = $this->createAuthorizedUser();

        $client->request('GET', "/api/v2/orders", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function test_get_v1_unauthorized()
    {
        $client = $this->createUnauthorizedClient();

        $client->request('GET', "/api/v1/orders/1", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function test_get_v2_unauthorized()
    {
        $client = $this->createUnauthorizedClient();

        $client->request('GET', "/api/v2/orders/1", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function test_post_unauthorized()
    {
        $client = $this->createUnauthorizedClient();

        $client->request('POST', "/api/v1/orders", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ], json_encode([]));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function test_post()
    {
        $client = $this->createUnauthorizedClient();
        $userService = $client->getContainer()->get(UserService::class);
        $mediaService = $client->getContainer()->get(MediaService::class);
        $partnerCategoryService = $client->getContainer()->get(PartnerCategoryService::class);
        $em = $client->getContainer()->get('doctrine')->getManager();

        $code = $em->getRepository(PartnerPostalCode::class)->findOneBy([]);
        if (!$code) {
            $this->fail('PartnerPostalCode not found');
        }

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
                'postalCode' => $code->getPostalCode(),
            ],
            'scheduledAt' => date('Y-m-d 23:59:00'),
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


        $user = $userService->create([
            'name' => md5(uniqid()),
            'email' => md5(uniqid()),
            'password' => '12345',
            'creditCards' => [
                [
                    'token' => md5(uniqid()),
                    'name' => '4242'
                ]
            ]
        ]);

        $accessToken = $user->getAccessToken();

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

    public function test_put_v1_unauthorized()
    {
        $client = $this->createUnauthorizedClient();

        $client->request('PUT', "/api/v1/orders/1", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ], json_encode([]));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function test_put_v1_user()
    {
        $client = $this->createUnauthorizedClient();

        $userService = $client->getContainer()->get(UserService::class);
        $partnerCategoryService = $client->getContainer()->get(PartnerCategoryService::class);
        $em = $client->getContainer()->get('doctrine')->getManager();

        $code = $em->getRepository(PartnerPostalCode::class)->findOneBy([]);
        if (!$code) {
            $this->fail('PartnerPostalCode not found');
        }

        $category = $partnerCategoryService->findOneByFilter([
            'isSelectable' => true,
            'hasPrice' => true,
        ]);
        if (!$category) {
            $this->fail('Partner categories not found');
        }

        $category1 = $category->getCategory()->getId();

        $content = [
            'location' => [
                'lat' => 12.12345,
                'lng' => 21.12345,
                'address' => md5(uniqid()),
                'postalCode' => $code->getPostalCode()
            ],
            'scheduledAt' => date('Y-m-d 23:59:00'),
            'items' => [
                [
                    'category' => $category1,
                    'quantity' => 1
                ]
            ],
            'message' => [
                'text' => md5(uniqid()),
            ]
        ];

        $user = $userService->create([
            'name' => md5(uniqid()),
            'email' => md5(uniqid()),
            'password' => '12345',
            'creditCards' => [
                [
                    'token' => md5(uniqid()),
                    'name' => '4242'
                ]
            ]
        ]);

        $accessToken = $user->getAccessToken();

        $client->request('POST', "/api/v1/orders", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'HTTP_Authorization' => $accessToken
        ], json_encode($content));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $client->request('PUT', "/api/v1/orders/" . $content['id'], [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'HTTP_Authorization' => $accessToken
        ], json_encode([
            'status' => OrderStatus::CANCELED
        ]));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['status']), 'Missing status');
        $this->assertEquals(OrderStatus::CANCELED, $content['status']);
    }

    public function test_put_v2_unauthorized()
    {
        $client = $this->createUnauthorizedClient();

        $client->request('PUT', "/api/v2/orders/1", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ], json_encode([]));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function test_put_v2_forbidden_user()
    {
        $client = $this->createAuthorizedUser();

        $accessToken = $this->getUserAccessToken();

        $client->request('PUT', "/api/v2/orders/1", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'HTTP_Authorization' => $accessToken
        ], json_encode([]));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_FORBIDDEN, $response->getStatusCode());
    }
}