<?php

namespace App\Tests\Controller;

use App\Entity\CategoryType;
use App\Entity\OrderRepeat;
use App\Entity\OrderStatus;
use App\Entity\PartnerPostalCode;
use App\Entity\PartnerStatus;
use App\Entity\PaymentType;
use App\Service\CategoryService;
use App\Service\MediaService;
use App\Service\PartnerCategoryService;
use App\Service\PartnerService;
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

    public function test_post_recycling()
    {
        $client = $this->createAuthorizedAdmin();

        $userService = $client->getContainer()->get(UserService::class);
        $mediaService = $client->getContainer()->get(MediaService::class);
        $partnerCategoryService = $client->getContainer()->get(PartnerCategoryService::class);
        $partnerService = $client->getContainer()->get(PartnerService::class);
        $em = $client->getContainer()->get('doctrine')->getManager();

        $partner = $partnerService->create([
            'accountId' => md5(uniqid()),
            'status' => PartnerStatus::APPROVED,
            'postalCodes' => [
                [
                    'postalCode' => mt_rand(10000, 99999),
                    'type' => CategoryType::RECYCLING
                ],
            ],
            'user' => [
                'name' => md5(uniqid()),
                'email' => md5(uniqid()) . '@mail.com',
                'password' => '12345',
            ],
            'location' => [
                'lat' => 9.9999,
                'lng' => 1.1111,
                'address' => md5(uniqid()),
                'postalCode' => '00001'
            ]

        ]);

        $client = $this->createUnauthorizedClient();

        $path = '/tmp/OrderRESTControllerTest.txt';
        file_put_contents($path, md5(uniqid()));

        $file = new UploadedFile($path, 'OrderRESTControllerTest.txt', 'text/plain', UPLOAD_ERR_OK, true);

        $media = $mediaService->create($file);

        /** @var PartnerPostalCode $code */
        $code = $em->getRepository(PartnerPostalCode::class)->findOneBy([
            'partner' => $partner->getId(),
            'type' => CategoryType::RECYCLING
        ]);
        if (!$code) {
            $this->fail('PartnerPostalCode not found');
        }

        $categories = $partnerCategoryService->findByFilter([
            'partner' => $partner->getId(),
            'type' => CategoryType::RECYCLING,
            'isSelectable' => true,
        ], 1, 2);
        if (count($categories) !== 2) {
            $this->fail('PartnerCategory not found');
        }

        $repeatables = [null, OrderRepeat::WEEK, OrderRepeat::MONTH, OrderRepeat::MONTH_3];

        $this->assertEquals(CategoryType::RECYCLING, $categories[0]->getCategory()->getType());
        $this->assertEquals(CategoryType::RECYCLING, $categories[1]->getCategory()->getType());

        $category1 = $categories[0]->getCategory()->getId();
        $category2 = $categories[1]->getCategory()->getId();

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
                    'quantity' => 1
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
                    'isPrimary' => true,
                    'lastFour' => '4242'
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

        $this->assertTrue(isset($content['status']), 'Missing status');
        $this->assertEquals(OrderStatus::CREATED, $content['status']);

        $this->assertTrue(isset($content['type']), 'Missing type');
        $this->assertEquals(CategoryType::RECYCLING, $content['type']);

        $this->assertTrue(isset($content['location']['id']), 'Missing location.id');
        $this->assertTrue(isset($content['partner']['id']), 'Missing partner.id');
        $this->assertTrue(isset($content['items']), 'Missing items');
        $this->assertTrue(isset($content['price']), 'Missing price');

        $this->assertTrue(isset($content['message']), 'Missing message');
        $this->assertTrue(isset($content['message']['media']), 'Missing message.media');
        $this->assertEquals(1, count($content['message']['media']), 'Missing message.media');
        $this->assertTrue(isset($content['message']['media'][0]['url']), 'Missing message.media.url');

        foreach ($content['items'] as $item) {
            $this->assertTrue(isset($item['category']['id']), 'Missing item.category.id');
            $this->assertTrue(isset($item['partnerCategory']['id']), 'Missing item.partnerCategory.id');
            $this->assertTrue(isset($item['price']), 'Missing item.price');
        }
    }

    public function test_post_junk_removal()
    {
        $client = $this->createAuthorizedAdmin();

        $userService = $client->getContainer()->get(UserService::class);
        $mediaService = $client->getContainer()->get(MediaService::class);
        $partnerCategoryService = $client->getContainer()->get(PartnerCategoryService::class);
        $partnerService = $client->getContainer()->get(PartnerService::class);
        $em = $client->getContainer()->get('doctrine')->getManager();

        $partner = $partnerService->create([
            'accountId' => md5(uniqid()),
            'status' => PartnerStatus::APPROVED,
            'postalCodes' => [
                [
                    'postalCode' => mt_rand(10000, 99999),
                    'type' => CategoryType::JUNK_REMOVAL
                ],
            ],
            'user' => [
                'name' => md5(uniqid()),
                'email' => md5(uniqid()) . '@mail.com',
                'password' => '12345',
            ],
            'location' => [
                'lat' => 9.9999,
                'lng' => 1.1111,
                'address' => md5(uniqid()),
                'postalCode' => '00001'
            ]

        ]);

        $client = $this->createUnauthorizedClient();

        /** @var PartnerPostalCode $code */
        $code = $em->getRepository(PartnerPostalCode::class)->findOneBy([
            'partner' => $partner->getId(),
            'type' => CategoryType::JUNK_REMOVAL
        ]);
        if (!$code) {
            $this->fail('PartnerPostalCode not found');
        }

        $path = '/tmp/OrderRESTControllerTest.txt';
        file_put_contents($path, md5(uniqid()));

        $file = new UploadedFile($path, 'OrderRESTControllerTest.txt', 'text/plain', UPLOAD_ERR_OK, true);

        $media = $mediaService->create($file);

        $categories = $partnerCategoryService->findByFilter([
            'partner' => $partner->getId(),
            'type' => CategoryType::JUNK_REMOVAL,
            'isSelectable' => true,
            'hasPrice' => true,
        ], 1, 2);
        if (count($categories) !== 2) {
            $this->fail('PartnerCategory not found');
        }

        $repeatables = [null, OrderRepeat::WEEK, OrderRepeat::MONTH, OrderRepeat::MONTH_3];

        $cat1 = $categories[0]->getCategory();
        $cat2 = $categories[1]->getCategory();

        $this->assertEquals(CategoryType::JUNK_REMOVAL, $cat1->getType());
        $this->assertEquals(CategoryType::JUNK_REMOVAL, $cat2->getType());

        $category1 = $cat1->getId();
        $price1 = $categories[0]->getPrice();

        $category2 = $cat2->getId();
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
                    'isPrimary' => true,
                    'lastFour' => '4242'
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

        $this->assertTrue(isset($content['status']), 'Missing status');
        $this->assertEquals(OrderStatus::CREATED, $content['status']);

        $this->assertTrue(isset($content['type']), 'Missing type');
        $this->assertEquals(CategoryType::JUNK_REMOVAL, $content['type']);

        $this->assertTrue(isset($content['location']['id']), 'Missing location.id');
        $this->assertTrue(isset($content['partner']['id']), 'Missing partner.id');
        $this->assertTrue(isset($content['items']), 'Missing items');
        $this->assertTrue(isset($content['price']), 'Missing price');

        $this->assertTrue(isset($content['message']), 'Missing message');
        $this->assertTrue(isset($content['message']['media']), 'Missing message.media');
        $this->assertEquals(1, count($content['message']['media']), 'Missing message.media');
        $this->assertTrue(isset($content['message']['media'][0]['url']), 'Missing message.media.url');

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

    public function test_post_junk_removal_with_item_message()
    {
        $client = $this->createAuthorizedAdmin();

        $userService = $client->getContainer()->get(UserService::class);
        $mediaService = $client->getContainer()->get(MediaService::class);
        $partnerCategoryService = $client->getContainer()->get(PartnerCategoryService::class);
        $partnerService = $client->getContainer()->get(PartnerService::class);
        $em = $client->getContainer()->get('doctrine')->getManager();
        $root = $client->getContainer()->getParameter('kernel.root_dir') . '/../public';

        $partner = $partnerService->create([
            'accountId' => md5(uniqid()),
            'status' => PartnerStatus::APPROVED,
            'postalCodes' => [
                [
                    'postalCode' => mt_rand(10000, 99999),
                    'type' => CategoryType::JUNK_REMOVAL
                ],
            ],
            'user' => [
                'name' => md5(uniqid()),
                'email' => md5(uniqid()) . '@mail.com',
                'password' => '12345',
            ],
            'location' => [
                'lat' => 9.9999,
                'lng' => 1.1111,
                'address' => md5(uniqid()),
                'postalCode' => '00001'
            ]

        ]);

        $client = $this->createUnauthorizedClient();

        /** @var PartnerPostalCode $code */
        $code = $em->getRepository(PartnerPostalCode::class)->findOneBy([
            'partner' => $partner->getId(),
            'type' => CategoryType::JUNK_REMOVAL
        ]);
        if (!$code) {
            $this->fail('PartnerPostalCode not found');
        }

        $path1 = $root . '/img/favicon/apple-touch-icon-114x114.png';
        $path2 = $root . '/img/favicon/apple-touch-icon-152x152.png';
        $path3 = $root . '/img/favicon/favicon-128.png';

        copy($path1, '/tmp/apple-touch-icon-114x114.png');
        copy($path2, '/tmp/apple-touch-icon-152x152.png');
        copy($path3, '/tmp/favicon-128.png');

        $file1 = new UploadedFile('/tmp/apple-touch-icon-114x114.png', 'apple-touch-icon-114x114.png', 'image/png', UPLOAD_ERR_OK, true);
        $file2 = new UploadedFile('/tmp/apple-touch-icon-152x152.png', 'apple-touch-icon-152x152.png', 'image/png', UPLOAD_ERR_OK, true);
        $file3 = new UploadedFile('/tmp/favicon-128.png', 'favicon-128.png', 'image/png', UPLOAD_ERR_OK, true);

        $media1 = $mediaService->create($file1);
        $media2 = $mediaService->create($file2);
        $media3 = $mediaService->create($file3);

        $categories = $partnerCategoryService->findByFilter([
            'partner' => $partner->getId(),
            'type' => CategoryType::JUNK_REMOVAL,
            'isSelectable' => true,
            'hasPrice' => true,
        ], 1, 2);
        if (count($categories) !== 2) {
            $this->fail('PartnerCategory not found');
        }

        $repeatables = [null, OrderRepeat::WEEK, OrderRepeat::MONTH, OrderRepeat::MONTH_3];

        $cat1 = $categories[0]->getCategory();
        $cat2 = $categories[1]->getCategory();

        $this->assertEquals(CategoryType::JUNK_REMOVAL, $cat1->getType());
        $this->assertEquals(CategoryType::JUNK_REMOVAL, $cat2->getType());

        $category1 = $cat1->getId();
        $price1 = $categories[0]->getPrice();

        $category2 = $cat2->getId();
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
                    'quantity' => 1,
                    'message' => [
                        'text' => md5(uniqid()),
                        'files' => [
                            $media1->getId()
                        ]
                    ]
                ],
                [
                    'category' => $category2,
                    'quantity' => 10,
                    'message' => [
                        'text' => md5(uniqid()),
                        'files' => [
                            $media2->getId()
                        ]
                    ]
                ]
            ],
            'message' => [
                'text' => md5(uniqid()),
                'files' => [
                    $media3->getId()
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
                    'isPrimary' => true,
                    'lastFour' => '4242'
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

        $this->assertTrue(isset($content['status']), 'Missing status');
        $this->assertEquals(OrderStatus::CREATED, $content['status']);

        $this->assertTrue(isset($content['type']), 'Missing type');
        $this->assertEquals(CategoryType::JUNK_REMOVAL, $content['type']);

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

            $this->assertTrue(isset($item['message']), 'Missing item.message');
            $this->assertTrue(isset($item['message']['text']), 'Missing item.message.text');
        }

        $this->assertEquals($priceTotal, $content['price'], 'Invalid price');
    }

    public function test_new_location_should_be_added_to_user_on_new_order()
    {
        $client = $this->createAuthorizedAdmin();

        $userService = $client->getContainer()->get(UserService::class);
        $partnerCategoryService = $client->getContainer()->get(PartnerCategoryService::class);
        $partnerService = $client->getContainer()->get(PartnerService::class);

        $partner = $partnerService->create([
            'accountId' => md5(uniqid()),
            'status' => PartnerStatus::APPROVED,
            'postalCodes' => [
                [
                    'postalCode' => mt_rand(10000, 99999),
                    'type' => CategoryType::RECYCLING
                ],
            ],
            'user' => [
                'name' => md5(uniqid()),
                'email' => md5(uniqid()) . '@mail.com',
                'password' => '12345',
            ],
            'location' => [
                'address' => md5(uniqid()),
            ]
        ]);

        /** @var PartnerPostalCode $code */
        $code = $partner->getPostalCodes()->get(0);

        $categories = $partnerCategoryService->findByFilter([
            'partner' => $partner->getId(),
            'type' => $code->getType(),
            'isSelectable' => true,
        ], 1, 1);
        if (count($categories) !== 1) {
            $this->fail('PartnerCategory not found');
        }

        $repeatables = [null, OrderRepeat::WEEK, OrderRepeat::MONTH, OrderRepeat::MONTH_3];

        $this->assertEquals($code->getType(), $categories[0]->getCategory()->getType());

        $category1 = $categories[0]->getCategory()->getId();

        $orderLocation = [
            'lat' => 12.12345,
            'lng' => 21.12345,
            'city' => md5(uniqid()),
            'address' => md5(uniqid()),
            'postalCode' => $code->getPostalCode(),
        ];

        $content = [
            'location' => $orderLocation,
            'scheduledAt' => date('Y-m-d 23:59:00'),
            'repeatable' => $repeatables[array_rand($repeatables)],
            'items' => [
                [
                    'category' => $category1,
                    'quantity' => 1
                ]
            ],
            'message' => [
                'text' => md5(uniqid())
            ]
        ];

        $user = $userService->create([
            'name' => md5(uniqid()),
            'email' => md5(uniqid()),
            'password' => '12345',
            'creditCards' => [
                [
                    'token' => md5(uniqid()),
                    'isPrimary' => true,
                    'lastFour' => '4242'
                ]
            ]
        ]);

        $this->assertEquals(0, $user->getLocations()->count());

        $client = $this->createUnauthorizedClient();

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
        $this->assertTrue(isset($content['location']['id']), 'Missing location.id');

        $client->request('GET', "/api/v1/me", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'HTTP_Authorization' => $accessToken
        ]);

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['locations']), 'Missing status');

        $this->assertEquals(1, count($content['locations']));

        $location = $content['locations'][0];

        $this->assertEquals($orderLocation['address'], $location['address']);
        $this->assertEquals($orderLocation['city'], $location['city']);
        $this->assertEquals($orderLocation['postalCode'], $location['postalCode']);
    }

    public function test_same_location_should_not_be_added_to_user_on_new_order()
    {
        $client = $this->createAuthorizedAdmin();

        $userService = $client->getContainer()->get(UserService::class);
        $partnerCategoryService = $client->getContainer()->get(PartnerCategoryService::class);
        $partnerService = $client->getContainer()->get(PartnerService::class);

        $partner = $partnerService->create([
            'accountId' => md5(uniqid()),
            'status' => PartnerStatus::APPROVED,
            'postalCodes' => [
                [
                    'postalCode' => mt_rand(10000, 99999),
                    'type' => CategoryType::RECYCLING
                ],
            ],
            'user' => [
                'name' => md5(uniqid()),
                'email' => md5(uniqid()) . '@mail.com',
                'password' => '12345',
            ],
            'location' => [
                'address' => md5(uniqid()),
            ]
        ]);

        /** @var PartnerPostalCode $code */
        $code = $partner->getPostalCodes()->get(0);

        $categories = $partnerCategoryService->findByFilter([
            'partner' => $partner->getId(),
            'type' => $code->getType(),
            'isSelectable' => true,
        ], 1, 1);
        if (count($categories) !== 1) {
            $this->fail('PartnerCategory not found');
        }

        $repeatables = [null, OrderRepeat::WEEK, OrderRepeat::MONTH, OrderRepeat::MONTH_3];

        $this->assertEquals($code->getType(), $categories[0]->getCategory()->getType());

        $category1 = $categories[0]->getCategory()->getId();

        $orderLocation = [
            'lat' => 12.12345,
            'lng' => 21.12345,
            'city' => md5(uniqid()),
            'address' => md5(uniqid()),
            'postalCode' => $code->getPostalCode(),
        ];

        $content1 = [
            'location' => $orderLocation,
            'scheduledAt' => date('Y-m-d 23:59:00'),
            'repeatable' => $repeatables[array_rand($repeatables)],
            'items' => [
                [
                    'category' => $category1,
                    'quantity' => 1
                ]
            ],
            'message' => [
                'text' => md5(uniqid())
            ]
        ];

        $content2 = [
            'location' => $orderLocation,
            'scheduledAt' => date('Y-m-d 23:59:00'),
            'repeatable' => $repeatables[array_rand($repeatables)],
            'items' => [
                [
                    'category' => $category1,
                    'quantity' => 100
                ]
            ],
            'message' => [
                'text' => md5(uniqid())
            ]
        ];

        $user = $userService->create([
            'name' => md5(uniqid()),
            'email' => md5(uniqid()),
            'password' => '12345',
            'creditCards' => [
                [
                    'token' => md5(uniqid()),
                    'isPrimary' => true,
                    'lastFour' => '4242'
                ]
            ]
        ]);

        $client = $this->createUnauthorizedClient();

        $accessToken = $user->getAccessToken();

        $client->request('POST', "/api/v1/orders", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'HTTP_Authorization' => $accessToken
        ], json_encode($content1));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['location']['id']), 'Missing location.id');

        $client->request('POST', "/api/v1/orders", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'HTTP_Authorization' => $accessToken
        ], json_encode($content2));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['location']['id']), 'Missing location.id');

        $client->request('GET', "/api/v1/me", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'HTTP_Authorization' => $accessToken
        ]);

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['locations']), 'Missing status');

        $this->assertEquals(1, count($content['locations']));

        $location = $content['locations'][0];

        $this->assertEquals($orderLocation['address'], $location['address']);
        $this->assertEquals($orderLocation['city'], $location['city']);
        $this->assertEquals($orderLocation['postalCode'], $location['postalCode']);
    }

    public function test_put_v1_user_cancel_order()
    {
        $client = $this->createAuthorizedAdmin();

        $userService = $client->getContainer()->get(UserService::class);
        $partnerService = $client->getContainer()->get(PartnerService::class);
        $categoryService = $client->getContainer()->get(CategoryService::class);
        $partnerCategoryService = $client->getContainer()->get(PartnerCategoryService::class);

        $partner = $partnerService->create([
            'accountId' => md5(uniqid()),
            'status' => PartnerStatus::APPROVED,
            'postalCodes' => [
                [
                    'postalCode' => mt_rand(10000, 99999),
                    'type' => CategoryType::JUNK_REMOVAL
                ],
            ],
            'user' => [
                'name' => md5(uniqid()),
                'email' => md5(uniqid()) . '@mail.com',
                'password' => '12345',
            ],
            'location' => [
                'address' => md5(uniqid()),
            ]
        ]);

        $category = $categoryService->create([
            'name' => md5(uniqid()),
            'price' => 1000,
            'hasPrice' => true,
            'isSelectable' => true,
            'type' => CategoryType::JUNK_REMOVAL,
        ], false);

        $partnerCategoryService->create($partner, $category);

        $postalCode = $partner->getPostalCodes()->get(0)->getPostalCode();

        $content = [
            'location' => [
                'lat' => 12.12345,
                'lng' => 21.12345,
                'address' => md5(uniqid()),
                'postalCode' => $postalCode
            ],
            'scheduledAt' => date('Y-m-d 23:59:00'),
            'items' => [
                [
                    'category' => $category->getId(),
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
                    'isPrimary' => true,
                    'lastFour' => '4242'
                ]
            ]
        ]);

        $client = $this->createUnauthorizedClient();

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
        $this->assertTrue(isset($content['status']), 'Missing status');
        $this->assertEquals(OrderStatus::CREATED, $content['status']);

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

    public function test_refund_is_created_on_junk_removal_order_cancel()
    {
        $client = $this->createAuthorizedAdmin();

        $userService = $client->getContainer()->get(UserService::class);
        $partnerService = $client->getContainer()->get(PartnerService::class);
        $categoryService = $client->getContainer()->get(CategoryService::class);
        $partnerCategoryService = $client->getContainer()->get(PartnerCategoryService::class);

        $partner = $partnerService->create([
            'accountId' => md5(uniqid()),
            'status' => PartnerStatus::APPROVED,
            'postalCodes' => [
                [
                    'postalCode' => mt_rand(10000, 99999),
                    'type' => CategoryType::JUNK_REMOVAL
                ],
            ],
            'user' => [
                'name' => md5(uniqid()),
                'email' => md5(uniqid()) . '@mail.com',
                'password' => '12345',
            ],
            'location' => [
                'address' => md5(uniqid()),
            ]
        ]);

        $category = $categoryService->create([
            'name' => md5(uniqid()),
            'price' => 1000,
            'hasPrice' => true,
            'isSelectable' => true,
            'type' => CategoryType::JUNK_REMOVAL,
        ], false);

        $partnerCategoryService->create($partner, $category);

        $postalCode = $partner->getPostalCodes()->get(0)->getPostalCode();

        $content = [
            'location' => [
                'lat' => 12.12345,
                'lng' => 21.12345,
                'city' => md5(uniqid()),
                'address' => md5(uniqid()),
                'postalCode' => $postalCode,
            ],
            'scheduledAt' => date('Y-m-d 23:59:00'),
            'items' => [
                [
                    'category' => $category->getId(),
                    'quantity' => 100
                ]
            ],
            'message' => [
                'text' => md5(uniqid())
            ]
        ];

        $user = $userService->create([
            'name' => md5(uniqid()),
            'phone' => md5(uniqid()),
            'email' => md5(uniqid()),
            'password' => '12345',
            'creditCards' => [
                [
                    'token' => md5(uniqid()),
                    'isPrimary' => true,
                    'lastFour' => '4242'
                ]
            ]
        ]);

        $client = $this->createUnauthorizedClient();

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
        $this->assertTrue(isset($content['status']), 'Missing status');
        $this->assertEquals(OrderStatus::CREATED, $content['status']);
        $this->assertTrue(isset($content['price']), 'Missing price');
        $this->assertTrue($content['price'] > 0, 'Invalid price');

        $orderId = $content['id'];

        $client = $this->createAuthorizedAdmin();

        $client->request('GET', "/api/v2/orders/$orderId", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['payments']), 'Missing payments');
        $this->assertEquals(1, count($content['payments']), 'Invalid payments');
        $this->assertEquals(PaymentType::PAYMENT, $content['payments'][0]['type'], 'Invalid payments.0.type');

        $client->request('PUT', "/api/v2/orders/$orderId", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ], json_encode([
            'status' => OrderStatus::CANCELED
        ]));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');

        $this->assertTrue(isset($content['status']), 'Missing status');
        $this->assertEquals(OrderStatus::CANCELED, $content['status']);

        $this->assertTrue(isset($content['payments']), 'Missing payments');
        $this->assertEquals(2, count($content['payments']), 'Invalid payments');
        $this->assertEquals(PaymentType::PAYMENT, $content['payments'][0]['type'], 'Invalid payments.0.type');
        $this->assertTrue($content['payments'][0]['isRefunded'], 'Invalid payments.0.isRefunded');
        $this->assertEquals(PaymentType::REFUND, $content['payments'][1]['type'], 'Invalid payments.1.type');
    }

    public function test_refund_is_created_on_recycling_order_cancel()
    {
        $client = $this->createAuthorizedAdmin();

        $userService = $client->getContainer()->get(UserService::class);
        $partnerService = $client->getContainer()->get(PartnerService::class);
        $categoryService = $client->getContainer()->get(CategoryService::class);
        $partnerCategoryService = $client->getContainer()->get(PartnerCategoryService::class);

        $partner = $partnerService->create([
            'accountId' => md5(uniqid()),
            'status' => PartnerStatus::APPROVED,
            'postalCodes' => [
                [
                    'postalCode' => mt_rand(10000, 99999),
                    'type' => CategoryType::RECYCLING
                ],
            ],
            'user' => [
                'name' => md5(uniqid()),
                'email' => md5(uniqid()) . '@mail.com',
                'password' => '12345',
            ],
            'location' => [
                'address' => md5(uniqid()),
            ]
        ]);

        $category = $categoryService->create([
            'name' => md5(uniqid()),
            'price' => 1000,
            'hasPrice' => true,
            'isSelectable' => true,
            'type' => CategoryType::RECYCLING,
        ], false);

        $partnerCategoryService->create($partner, $category);

        $postalCode = $partner->getPostalCodes()->get(0)->getPostalCode();

        $content = [
            'location' => [
                'lat' => 12.12345,
                'lng' => 21.12345,
                'city' => md5(uniqid()),
                'address' => md5(uniqid()),
                'postalCode' => $postalCode,
            ],
            'scheduledAt' => date('Y-m-d 23:59:00'),
            'items' => [
                [
                    'category' => $category->getId(),
                    'quantity' => 100
                ]
            ],
            'message' => [
                'text' => md5(uniqid())
            ]
        ];

        $user = $userService->create([
            'name' => md5(uniqid()),
            'phone' => md5(uniqid()),
            'email' => md5(uniqid()),
            'password' => '12345',
            'creditCards' => [
                [
                    'token' => md5(uniqid()),
                    'isPrimary' => true,
                    'lastFour' => '4242'
                ]
            ]
        ]);

        $client = $this->createUnauthorizedClient();

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
        $this->assertTrue(isset($content['status']), 'Missing status');
        $this->assertEquals(OrderStatus::CREATED, $content['status']);
        $this->assertTrue(isset($content['price']), 'Missing price');
        $this->assertTrue($content['price'] > 0, 'Invalid price');

        $orderId = $content['id'];

        $client = $this->createAuthorizedAdmin();

        $client->request('GET', "/api/v2/orders/$orderId", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['payments']), 'Missing payments');
        $this->assertEquals(1, count($content['payments']), 'Invalid payments');
        $this->assertEquals(PaymentType::PAYMENT, $content['payments'][0]['type'], 'Invalid payments.0.type');

        $client->request('PUT', "/api/v2/orders/$orderId", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ], json_encode([
            'status' => OrderStatus::CANCELED
        ]));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');

        $this->assertTrue(isset($content['status']), 'Missing status');
        $this->assertEquals(OrderStatus::CANCELED, $content['status']);

        $this->assertTrue(isset($content['payments']), 'Missing payments');
        $this->assertEquals(2, count($content['payments']), 'Invalid payments');
        $this->assertEquals(PaymentType::PAYMENT, $content['payments'][0]['type'], 'Invalid payments.0.type');
        $this->assertTrue($content['payments'][0]['isRefunded'], 'Invalid payments.0.isRefunded');
        $this->assertEquals(PaymentType::REFUND, $content['payments'][1]['type'], 'Invalid payments.1.type');
    }
}
