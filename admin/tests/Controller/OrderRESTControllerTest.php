<?php

namespace App\Tests\Controller;

use App\Entity\CategoryType;
use App\Entity\OrderRepeat;
use App\Entity\OrderStatus;
use App\Entity\PaymentStatus;
use App\Entity\PaymentType;
use App\Service\MediaService;
use App\Service\UserService;
use App\Tests\Classes\CountryCreator;
use App\Tests\Classes\PartnerCategoryCreator;
use App\Tests\Classes\PartnerCreator;
use App\Tests\Classes\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @covers \App\Controller\OrderRESTController
 */
class OrderRESTControllerTest extends WebTestCase
{

    use CountryCreator;
    use PartnerCreator;
    use PartnerCategoryCreator;

    /**
     * @small
     */
    public function test_gets_v1_unauthorized()
    {
        $client = $this->createUnauthorizedClient();

        $client->xmlHttpRequest('GET', "/api/v1/orders");

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /**
     * @small
     */
    public function test_gets_v2_unauthorized()
    {
        $client = $this->createUnauthorizedClient();

        $client->xmlHttpRequest('GET', "/api/v2/orders");

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /**
     * @small
     */
    public function test_gets_v2_forbidden_user()
    {
        $client = $this->createAuthorizedUser();

        $client->xmlHttpRequest('GET', "/api/v2/orders");

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    /**
     * @small
     */
    public function test_get_v1_unauthorized()
    {
        $client = $this->createUnauthorizedClient();

        $client->xmlHttpRequest('GET', "/api/v1/orders/1");

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /**
     * @small
     */
    public function test_get_v2_unauthorized()
    {
        $client = $this->createUnauthorizedClient();

        $client->xmlHttpRequest('GET', "/api/v2/orders/1");

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /**
     * @small
     */
    public function test_post_unauthorized()
    {
        $client = $this->createUnauthorizedClient();

        $client->xmlHttpRequest('POST', "/api/v1/orders");

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /**
     * @medium
     */
    public function test_post_recycling()
    {
        $client = $this->createAuthorizedAdmin();

        $userService = $client->getContainer()->get(UserService::class);
        $mediaService = $client->getContainer()->get(MediaService::class);
        $root = $client->getContainer()->getParameter('kernel.root_dir') . '/../public';

        $partner = $this->createPartner($client->getContainer(), CategoryType::RECYCLING);

        $partnerCategory1 = $this->createPartnerCategory($client->getContainer(), $partner, CategoryType::RECYCLING);
        $partnerCategory2 = $this->createPartnerCategory($client->getContainer(), $partner, CategoryType::RECYCLING);

        $client = $this->createUnauthorizedClient();

        $path1 = $root . '/img/favicon/apple-touch-icon-114x114.png';

        copy($path1, '/tmp/apple-touch-icon-114x114.png');

        $file = new UploadedFile('/tmp/apple-touch-icon-114x114.png', 'apple-touch-icon-114x114.png', 'image/png', UPLOAD_ERR_OK, true);

        $media = $mediaService->create($file);

        $repeatable = [null, OrderRepeat::WEEK, OrderRepeat::MONTH, OrderRepeat::MONTH_3];

        $category1 = $partnerCategory1->getId();
        $category2 = $partnerCategory2->getId();

        $postalCode = $partner->getPostalCodes()->get(0)->getPostalCode();

        $content = [
            'location' => [
                'lat' => 12.12345,
                'lng' => 21.12345,
                'address' => md5(uniqid()),
                'postalCode' => $postalCode,
            ],
            'partner' => $partner->getId(),
            'scheduledAt' => date('Y-m-d 23:59:00'),
            'repeatable' => $repeatable[array_rand($repeatable)],
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

        $client->xmlHttpRequest('POST', "/api/v1/orders", [], [], [
            'HTTP_Content-Type' => 'application/json',
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
        $this->assertTrue(isset($content['type']['key']), 'Missing type.key');
        $this->assertEquals(CategoryType::RECYCLING, $content['type']['key']);

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

    /**
     * @medium
     */
    public function test_post_junk_removal()
    {
        $client = $this->createAuthorizedAdmin();

        $userService = $client->getContainer()->get(UserService::class);
        $mediaService = $client->getContainer()->get(MediaService::class);
        $root = $client->getContainer()->getParameter('kernel.root_dir') . '/../public';

        $partner = $this->createPartner($client->getContainer(), CategoryType::JUNK_REMOVAL);

        $partnerCategory1 = $this->createPartnerCategory($client->getContainer(), $partner, CategoryType::JUNK_REMOVAL);
        $partnerCategory2 = $this->createPartnerCategory($client->getContainer(), $partner, CategoryType::JUNK_REMOVAL);

        $path1 = $root . '/img/favicon/apple-touch-icon-114x114.png';

        copy($path1, '/tmp/apple-touch-icon-114x114.png');

        $file = new UploadedFile('/tmp/apple-touch-icon-114x114.png', 'apple-touch-icon-114x114.png', 'image/png', UPLOAD_ERR_OK, true);

        $media = $mediaService->create($file);

        $repeatables = [null, OrderRepeat::WEEK, OrderRepeat::MONTH, OrderRepeat::MONTH_3];

        $category1 = $partnerCategory1->getId();
        $price1 = $partnerCategory1->getPrice();

        $category2 = $partnerCategory2->getId();
        $price2 = $partnerCategory2->getPrice();

        $priceTotal = $price1 + ($price2 * 10);

        $postalCode = $partner->getPostalCodes()->get(0)->getPostalCode();

        $content = [
            'location' => [
                'lat' => 12.12345,
                'lng' => 21.12345,
                'address' => md5(uniqid()),
                'postalCode' => $postalCode,
            ],
            'partner' => $partner->getId(),
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

        $client = $this->createUnauthorizedClient();

        $accessToken = $user->getAccessToken();

        $client->xmlHttpRequest('POST', "/api/v1/orders", [], [], [
            'HTTP_Content-Type' => 'application/json',
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
        $this->assertTrue(isset($content['type']['key']), 'Missing type.key');
        $this->assertEquals(CategoryType::JUNK_REMOVAL, $content['type']['key']);

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

            switch ($item['partnerCategory']['id']) {
                case $category1:
                    $this->assertEquals($price1, $item['price'], 'Invalid item.price');
                    break;
                case $category2:
                    $this->assertEquals($price2, $item['price'], 'Invalid item.price');
                    break;
                default:
                    $this->fail('Unknown item.partnerCategory.id');
            }
        }

        $this->assertEquals($priceTotal, $content['price'], 'Invalid price');
    }

    /**
     * @medium
     */
    public function test_put_v1_unauthorized()
    {
        $client = $this->createUnauthorizedClient();

        $client->xmlHttpRequest('PUT', "/api/v1/orders/1");

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /**
     * @medium
     */
    public function test_put_v2_unauthorized()
    {
        $client = $this->createUnauthorizedClient();

        $client->xmlHttpRequest('PUT', "/api/v2/orders/1");

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /**
     * @small
     */
    public function test_put_v2_forbidden_user()
    {
        $client = $this->createAuthorizedUser();

        $accessToken = $this->getUserAccessToken();

        $client->xmlHttpRequest('PUT', "/api/v2/orders/1", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_Authorization' => $accessToken
        ], json_encode([]));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    /**
     * @medium
     */
    public function test_post_junk_removal_with_item_message()
    {
        $client = $this->createAuthorizedAdmin();

        $userService = $client->getContainer()->get(UserService::class);
        $mediaService = $client->getContainer()->get(MediaService::class);
        $root = $client->getContainer()->getParameter('kernel.root_dir') . '/../public';

        $partner = $this->createPartner($client->getContainer(), CategoryType::JUNK_REMOVAL);

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

        $partnerCategory1 = $this->createPartnerCategory($client->getContainer(), $partner, CategoryType::JUNK_REMOVAL);
        $partnerCategory2 = $this->createPartnerCategory($client->getContainer(), $partner, CategoryType::JUNK_REMOVAL);

        $postalCode = $partner->getPostalCodes()->get(0)->getPostalCode();

        $category1 = $partnerCategory1->getId();
        $price1 = $partnerCategory1->getPrice();

        $category2 = $partnerCategory2->getId();
        $price2 = $partnerCategory2->getPrice();

        $priceTotal = $price1 + ($price2 * 10);

        $repeatables = [null, OrderRepeat::WEEK, OrderRepeat::MONTH, OrderRepeat::MONTH_3];

        $content = [
            'location' => [
                'lat' => 12.12345,
                'lng' => 21.12345,
                'address' => md5(uniqid()),
                'postalCode' => $postalCode,
            ],
            'partner' => $partner->getId(),
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

        $client = $this->createUnauthorizedClient();

        $accessToken = $user->getAccessToken();

        $client->xmlHttpRequest('POST', "/api/v1/orders", [], [], [
            'HTTP_Content-Type' => 'application/json',
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
        $this->assertTrue(isset($content['type']['key']), 'Missing type.key');
        $this->assertEquals(CategoryType::JUNK_REMOVAL, $content['type']['key']);

        $this->assertTrue(isset($content['location']['id']), 'Missing location.id');
        $this->assertTrue(isset($content['partner']['id']), 'Missing partner.id');
        $this->assertTrue(isset($content['items']), 'Missing items');
        $this->assertTrue(isset($content['price']), 'Missing price');

        foreach ($content['items'] as $item) {
            $this->assertTrue(isset($item['category']['id']), 'Missing item.category.id');
            $this->assertTrue(isset($item['partnerCategory']['id']), 'Missing item.partnerCategory.id');
            $this->assertTrue(isset($item['price']), 'Missing item.price');

            switch ($item['partnerCategory']['id']) {
                case $category1:
                    $this->assertEquals($price1, $item['price'], 'Invalid item.price');
                    break;
                case $category2:
                    $this->assertEquals($price2, $item['price'], 'Invalid item.price');
                    break;
                default:
                    $this->fail('Unknown item.partnerCategory.id');
            }

            $this->assertTrue(isset($item['message']), 'Missing item.message');
            $this->assertTrue(isset($item['message']['text']), 'Missing item.message.text');
        }

        $this->assertEquals($priceTotal, $content['price'], 'Invalid price');
    }

    /**
     * @medium
     */
    public function test_new_location_should_be_added_to_user_on_new_order()
    {
        $client = $this->createAuthorizedAdmin();

        $userService = $client->getContainer()->get(UserService::class);

        $partner = $this->createPartner($client->getContainer(), CategoryType::RECYCLING);

        $partnerCategory1 = $this->createPartnerCategory($client->getContainer(), $partner, CategoryType::RECYCLING);

        $postalCode = $partner->getPostalCodes()->get(0)->getPostalCode();

        $repeatables = [null, OrderRepeat::WEEK, OrderRepeat::MONTH, OrderRepeat::MONTH_3];

        $orderLocation = [
            'lat' => 12.12345,
            'lng' => 21.12345,
            'city' => md5(uniqid()),
            'address' => md5(uniqid()),
            'postalCode' => $postalCode,
        ];

        $content = [
            'location' => $orderLocation,
            'scheduledAt' => date('Y-m-d 23:59:00'),
            'repeatable' => $repeatables[array_rand($repeatables)],
            'partner' => $partner->getId(),
            'items' => [
                [
                    'category' => $partnerCategory1->getId(),
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

        $client->xmlHttpRequest('POST', "/api/v1/orders", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_Authorization' => $accessToken
        ], json_encode($content));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['location']['id']), 'Missing location.id');

        $client->xmlHttpRequest('GET', "/api/v1/me", [], [], [
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

    /**
     * @medium
     */
    public function test_same_location_should_not_be_added_to_user_on_new_order()
    {
        $client = $this->createAuthorizedAdmin();

        $userService = $client->getContainer()->get(UserService::class);

        $partner = $this->createPartner($client->getContainer(), CategoryType::JUNK_REMOVAL);

        $partnerCategory1 = $this->createPartnerCategory($client->getContainer(), $partner, CategoryType::JUNK_REMOVAL);

        $postalCode = $partner->getPostalCodes()->get(0)->getPostalCode();

        $orderLocation = [
            'lat' => 12.12345,
            'lng' => 21.12345,
            'city' => md5(uniqid()),
            'address' => md5(uniqid()),
            'postalCode' => $postalCode,
        ];

        $content1 = [
            'location' => $orderLocation,
            'scheduledAt' => date('Y-m-d 23:59:00'),
            'partner' => $partner->getId(),
            'items' => [
                [
                    'category' => $partnerCategory1->getId(),
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
            'partner' => $partner->getId(),
            'items' => [
                [
                    'category' => $partnerCategory1->getId(),
                    'quantity' => 100
                ]
            ],
            'message' => [
                'text' => md5(uniqid())
            ]
        ];

        $content3 = [
            'location' => $orderLocation,
            'scheduledAt' => date('Y-m-d 23:59:00'),
            'partner' => $partner->getId(),
            'items' => [
                [
                    'category' => $partnerCategory1->getId(),
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

        $client->xmlHttpRequest('POST', "/api/v1/orders", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_Authorization' => $accessToken
        ], json_encode($content1));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());

        $client->xmlHttpRequest('POST', "/api/v1/orders", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_Authorization' => $accessToken
        ], json_encode($content2));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());

        $client->xmlHttpRequest('POST', "/api/v1/orders", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_Authorization' => $accessToken
        ], json_encode($content3));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());

        $client->xmlHttpRequest('GET', "/api/v1/me", [], [], [
            'HTTP_Authorization' => $accessToken
        ]);

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['locations']), 'Missing locations');

        $this->assertEquals(1, count($content['locations']));

        $location = $content['locations'][0];

        $this->assertEquals($orderLocation['address'], $location['address']);
        $this->assertEquals($orderLocation['city'], $location['city']);
        $this->assertEquals($orderLocation['postalCode'], $location['postalCode']);
    }

    /**
     * @medium
     */
    public function test_put_v1_user_cancel_order()
    {
        $client = $this->createAuthorizedAdmin();

        $userService = $client->getContainer()->get(UserService::class);

        $partner = $this->createPartner($client->getContainer(), CategoryType::JUNK_REMOVAL);

        $partnerCategory1 = $this->createPartnerCategory($client->getContainer(), $partner, CategoryType::JUNK_REMOVAL);

        $postalCode = $partner->getPostalCodes()->get(0)->getPostalCode();

        $content = [
            'location' => [
                'lat' => 12.12345,
                'lng' => 21.12345,
                'address' => md5(uniqid()),
                'postalCode' => $postalCode
            ],
            'scheduledAt' => date('Y-m-d 23:59:00'),
            'partner' => $partner->getId(),
            'items' => [
                [
                    'category' => $partnerCategory1->getId(),
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

        $client->xmlHttpRequest('POST', "/api/v1/orders", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_Authorization' => $accessToken
        ], json_encode($content));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['status']), 'Missing status');
        $this->assertEquals(OrderStatus::CREATED, $content['status']);

        $client->xmlHttpRequest('PUT', "/api/v1/orders/" . $content['id'], [], [], [
            'HTTP_Content-Type' => 'application/json',
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

    /**
     * @medium
     */
    public function test_refund_is_created_on_junk_removal_order_cancel()
    {
        $client = $this->createAuthorizedAdmin();

        $userService = $client->getContainer()->get(UserService::class);

        $partner = $this->createPartner($client->getContainer(), CategoryType::JUNK_REMOVAL);

        $partnerCategory1 = $this->createPartnerCategory($client->getContainer(), $partner, CategoryType::JUNK_REMOVAL);

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
            'partner' => $partner->getId(),
            'items' => [
                [
                    'category' => $partnerCategory1->getId(),
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

        $client->xmlHttpRequest('POST', "/api/v1/orders", [], [], [
            'HTTP_Content-Type' => 'application/json',
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

        $client->xmlHttpRequest('GET', "/api/v2/orders/$orderId");

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['payments']), 'Missing payments');
        $this->assertEquals(1, count($content['payments']), 'Invalid payments');

        $payment = $content['payments'][0];
        $this->assertEquals(PaymentType::PAYMENT, $payment['type'], 'Invalid payments.0.type');
        $this->assertEquals(PaymentStatus::SUCCESS, $payment['status'], 'Invalid payments.0.status');

        $client->xmlHttpRequest('PUT', "/api/v2/orders/$orderId", [], [], [
            'HTTP_Content-Type' => 'application/json',
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

        $payment1 = $content['payments'][0];
        $payment2 = $content['payments'][1];

        $this->assertEquals(PaymentType::PAYMENT, $payment1['type'], 'Invalid payments.0.type');
        $this->assertEquals(PaymentStatus::SUCCESS, $payment1['status'], 'Invalid payments.0.status');
        $this->assertTrue($payment1['isRefunded'], 'Invalid payments.0.isRefunded');

        $this->assertEquals(PaymentType::REFUND, $payment2['type'], 'Invalid payments.1.type');
        $this->assertEquals(PaymentStatus::SUCCESS, $payment2['status'], 'Invalid payments.1.status');
    }

    /**
     * @medium
     */
    public function test_put_approve_order_admin()
    {
        $client = $this->createAuthorizedAdmin();

        $userService = $client->getContainer()->get(UserService::class);

        $partner = $this->createPartner($client->getContainer(), CategoryType::JUNK_REMOVAL);

        $partnerCategory1 = $this->createPartnerCategory($client->getContainer(), $partner, CategoryType::JUNK_REMOVAL);

        $postalCode = $partner->getPostalCodes()->get(0)->getPostalCode();

        $content = [
            'location' => [
                'lat' => 12.12345,
                'lng' => 21.12345,
                'address' => md5(uniqid()),
                'postalCode' => $postalCode
            ],
            'scheduledAt' => date('Y-m-d 23:59:00'),
            'partner' => $partner->getId(),
            'items' => [
                [
                    'category' => $partnerCategory1->getId(),
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

        $client->xmlHttpRequest('POST', "/api/v1/orders", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_Authorization' => $accessToken
        ], json_encode($content));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['status']), 'Missing status');
        $this->assertEquals(OrderStatus::CREATED, $content['status']);

        $client = $this->createAuthorizedAdmin();

        $client->xmlHttpRequest('PUT', "/api/v2/orders/" . $content['id'], [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_Authorization' => $accessToken
        ], json_encode([
            'status' => OrderStatus::APPROVED
        ]));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['status']), 'Missing status');
        $this->assertEquals(OrderStatus::APPROVED, $content['status']);
    }

    /**
     * @medium
     */
    public function test_put_reject_order_admin()
    {
        $client = $this->createAuthorizedAdmin();

        $userService = $client->getContainer()->get(UserService::class);

        $partner = $this->createPartner($client->getContainer(), CategoryType::JUNK_REMOVAL);

        $partnerCategory1 = $this->createPartnerCategory($client->getContainer(), $partner, CategoryType::JUNK_REMOVAL);

        $postalCode = $partner->getPostalCodes()->get(0)->getPostalCode();

        $content = [
            'location' => [
                'lat' => 12.12345,
                'lng' => 21.12345,
                'address' => md5(uniqid()),
                'postalCode' => $postalCode
            ],
            'scheduledAt' => date('Y-m-d 23:59:00'),
            'partner' => $partner->getId(),
            'items' => [
                [
                    'category' => $partnerCategory1->getId(),
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

        $client->xmlHttpRequest('POST', "/api/v1/orders", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_Authorization' => $accessToken
        ], json_encode($content));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['status']), 'Missing status');
        $this->assertEquals(OrderStatus::CREATED, $content['status']);

        $client = $this->createAuthorizedAdmin();

        $client->xmlHttpRequest('PUT', "/api/v2/orders/" . $content['id'], [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_Authorization' => $accessToken
        ], json_encode([
            'status' => OrderStatus::REJECTED
        ]));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['status']), 'Missing status');
        $this->assertEquals(OrderStatus::REJECTED, $content['status']);
    }

    /**
     * @medium
     */
    public function test_put_confirm_scheduled_at_order_admin()
    {
        $client = $this->createAuthorizedAdmin();

        $userService = $client->getContainer()->get(UserService::class);

        $partner = $this->createPartner($client->getContainer(), CategoryType::JUNK_REMOVAL);

        $partnerCategory1 = $this->createPartnerCategory($client->getContainer(), $partner, CategoryType::JUNK_REMOVAL);

        $postalCode = $partner->getPostalCodes()->get(0)->getPostalCode();

        $content = [
            'location' => [
                'lat' => 12.12345,
                'lng' => 21.12345,
                'address' => md5(uniqid()),
                'postalCode' => $postalCode
            ],
            'scheduledAt' => date('Y-m-d 23:00:00'),
            'partner' => $partner->getId(),
            'items' => [
                [
                    'category' => $partnerCategory1->getId(),
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

        $client->xmlHttpRequest('POST', "/api/v1/orders", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_Authorization' => $accessToken
        ], json_encode($content));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['status']), 'Missing status');
        $this->assertEquals(OrderStatus::CREATED, $content['status']);

        $client = $this->createAuthorizedAdmin();

        $client->xmlHttpRequest('PUT', "/api/v2/orders/" . $content['id'], [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_Authorization' => $accessToken
        ], json_encode([
            'isScheduleApproved' => true,
            'scheduledAt' => date('Y-m-d 23:30:00'),
        ]));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['status']), 'Missing status');
        $this->assertEquals(OrderStatus::CREATED, $content['status']);

        $this->assertTrue(isset($content['isScheduleApproved']), 'Missing isScheduleApproved');
        $this->assertTrue($content['isScheduleApproved'], 'Invalid isScheduleApproved');

        $this->assertTrue(isset($content['scheduledAt']), 'Missing scheduledAt');
        $this->assertEquals(date('Y-m-d 23:30:00'), $content['scheduledAt'], 'Invalid scheduledAt');
    }

    /**
     * @medium
     */
    public function test_put_confirm_price_at_order_admin()
    {
        $client = $this->createAuthorizedAdmin();

        $userService = $client->getContainer()->get(UserService::class);

        $partner = $this->createPartner($client->getContainer(), CategoryType::JUNK_REMOVAL);

        $partnerCategory1 = $this->createPartnerCategory($client->getContainer(), $partner, CategoryType::JUNK_REMOVAL);

        $postalCode = $partner->getPostalCodes()->get(0)->getPostalCode();

        $content = [
            'location' => [
                'lat' => 12.12345,
                'lng' => 21.12345,
                'address' => md5(uniqid()),
                'postalCode' => $postalCode
            ],
            'scheduledAt' => date('Y-m-d 23:00:00'),
            'partner' => $partner->getId(),
            'items' => [
                [
                    'category' => $partnerCategory1->getId(),
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

        $client->xmlHttpRequest('POST', "/api/v1/orders", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_Authorization' => $accessToken
        ], json_encode($content));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['status']), 'Missing status');
        $this->assertEquals(OrderStatus::CREATED, $content['status']);

        $client = $this->createAuthorizedAdmin();

        $newPrice = $content['price'] + 100;

        $client->xmlHttpRequest('PUT', "/api/v2/orders/" . $content['id'], [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_Authorization' => $accessToken
        ], json_encode([
            'isPriceApproved' => true,
            'price' => $newPrice
        ]));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['status']), 'Missing status');
        $this->assertEquals(OrderStatus::CREATED, $content['status']);

        $this->assertTrue(isset($content['isPriceApproved']), 'Missing isPriceApproved');
        $this->assertTrue($content['isPriceApproved'], 'Invalid isPriceApproved');

        $this->assertTrue(isset($content['price']), 'Missing price');
        $this->assertEquals($newPrice, $content['price'], 'Invalid price');
    }

    /**
     * @medium
     */
    public function test_put_in_progress_order_admin()
    {
        $client = $this->createAuthorizedAdmin();

        $userService = $client->getContainer()->get(UserService::class);

        $partner = $this->createPartner($client->getContainer(), CategoryType::JUNK_REMOVAL);

        $partnerCategory1 = $this->createPartnerCategory($client->getContainer(), $partner, CategoryType::JUNK_REMOVAL);

        $postalCode = $partner->getPostalCodes()->get(0)->getPostalCode();

        $content = [
            'location' => [
                'lat' => 12.12345,
                'lng' => 21.12345,
                'address' => md5(uniqid()),
                'postalCode' => $postalCode
            ],
            'scheduledAt' => date('Y-m-d 23:59:00'),
            'partner' => $partner->getId(),
            'items' => [
                [
                    'category' => $partnerCategory1->getId(),
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

        $client->xmlHttpRequest('POST', "/api/v1/orders", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_Authorization' => $accessToken
        ], json_encode($content));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['status']), 'Missing status');
        $this->assertEquals(OrderStatus::CREATED, $content['status']);

        $client = $this->createAuthorizedAdmin();

        $client->xmlHttpRequest('PUT', "/api/v2/orders/" . $content['id'], [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_Authorization' => $accessToken
        ], json_encode([
            'status' => OrderStatus::APPROVED
        ]));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['status']), 'Missing status');
        $this->assertEquals(OrderStatus::APPROVED, $content['status']);

        $client->xmlHttpRequest('PUT', "/api/v2/orders/" . $content['id'], [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_Authorization' => $accessToken
        ], json_encode([
            'status' => OrderStatus::IN_PROGRESS
        ]));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['status']), 'Missing status');
        $this->assertEquals(OrderStatus::IN_PROGRESS, $content['status']);
    }

    /**
     * @medium
     */
    public function test_post_recycling_failed_if_partner_cannot_manage_recycling_orders()
    {
        $client = $this->createAuthorizedAdmin();

        $userService = $client->getContainer()->get(UserService::class);
        $em = $client->getContainer()->get('doctrine')->getManager();

        $partner = $this->createPartner($client->getContainer(), CategoryType::RECYCLING);

        $partner->setCanManageRecyclingOrders(false);

        $em->persist($partner);
        $em->flush();

        $partnerCategory = $this->createPartnerCategory($client->getContainer(), $partner, CategoryType::RECYCLING);

        $client = $this->createUnauthorizedClient();

        $postalCode = $partner->getPostalCodes()->get(0)->getPostalCode();

        $content = [
            'location' => [
                'lat' => 12.12345,
                'lng' => 21.12345,
                'address' => md5(uniqid()),
                'postalCode' => $postalCode,
            ],
            'partner' => $partner->getId(),
            'scheduledAt' => date('Y-m-d 23:59:00'),
            'items' => [
                [
                    'category' => $partnerCategory->getId(),
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

        $accessToken = $user->getAccessToken();

        $client->xmlHttpRequest('POST', "/api/v1/orders", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_Authorization' => $accessToken
        ], json_encode($content));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['price']), 'Missing price');

        $this->assertTrue(isset($content['status']), 'Missing status');
        $this->assertEquals(OrderStatus::FAILED, $content['status']);
    }

    /**
     * @medium
     */
    public function test_post_junk_removal_failed_if_partner_cannot_manage_junk_removal_orders()
    {
        $client = $this->createAuthorizedAdmin();

        $userService = $client->getContainer()->get(UserService::class);
        $em = $client->getContainer()->get('doctrine')->getManager();

        $partner = $this->createPartner($client->getContainer(), CategoryType::JUNK_REMOVAL);

        $partner->setCanManageJunkRemovalOrders(false);

        $em->persist($partner);
        $em->flush();

        $partnerCategory = $this->createPartnerCategory($client->getContainer(), $partner, CategoryType::JUNK_REMOVAL);

        $client = $this->createUnauthorizedClient();

        $postalCode = $partner->getPostalCodes()->get(0)->getPostalCode();

        $content = [
            'location' => [
                'lat' => 12.12345,
                'lng' => 21.12345,
                'address' => md5(uniqid()),
                'postalCode' => $postalCode,
            ],
            'partner' => $partner->getId(),
            'scheduledAt' => date('Y-m-d 23:59:00'),
            'items' => [
                [
                    'category' => $partnerCategory->getId(),
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

        $accessToken = $user->getAccessToken();

        $client->xmlHttpRequest('POST', "/api/v1/orders", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_Authorization' => $accessToken
        ], json_encode($content));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['price']), 'Missing price');

        $this->assertTrue(isset($content['status']), 'Missing status');
        $this->assertEquals(OrderStatus::FAILED, $content['status']);
    }

    /**
     * @medium
     */
    public function test_post_donation_failed_if_partner_cannot_manage_donation_orders()
    {
        $client = $this->createAuthorizedAdmin();

        $userService = $client->getContainer()->get(UserService::class);
        $em = $client->getContainer()->get('doctrine')->getManager();

        $partner = $this->createPartner($client->getContainer(), CategoryType::DONATION);

        $partner->setCanManageDonationOrders(false);

        $em->persist($partner);
        $em->flush();

        $partnerCategory = $this->createPartnerCategory($client->getContainer(), $partner, CategoryType::DONATION);

        $client = $this->createUnauthorizedClient();

        $postalCode = $partner->getPostalCodes()->get(0)->getPostalCode();

        $content = [
            'location' => [
                'lat' => 12.12345,
                'lng' => 21.12345,
                'address' => md5(uniqid()),
                'postalCode' => $postalCode,
            ],
            'partner' => $partner->getId(),
            'scheduledAt' => date('Y-m-d 23:59:00'),
            'items' => [
                [
                    'category' => $partnerCategory->getId(),
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

        $accessToken = $user->getAccessToken();

        $client->xmlHttpRequest('POST', "/api/v1/orders", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_Authorization' => $accessToken
        ], json_encode($content));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['price']), 'Missing price');

        $this->assertTrue(isset($content['status']), 'Missing status');
        $this->assertEquals(OrderStatus::FAILED, $content['status']);
    }

    /**
     * @medium
     */
    public function test_post_shredding_failed_if_partner_cannot_manage_shredding_orders()
    {
        $client = $this->createAuthorizedAdmin();

        $userService = $client->getContainer()->get(UserService::class);
        $em = $client->getContainer()->get('doctrine')->getManager();

        $partner = $this->createPartner($client->getContainer(), CategoryType::SHREDDING);

        $partner->setCanManageShreddingOrders(false);

        $em->persist($partner);
        $em->flush();

        $partnerCategory = $this->createPartnerCategory($client->getContainer(), $partner, CategoryType::SHREDDING);

        $client = $this->createUnauthorizedClient();

        $postalCode = $partner->getPostalCodes()->get(0)->getPostalCode();

        $content = [
            'location' => [
                'lat' => 12.12345,
                'lng' => 21.12345,
                'address' => md5(uniqid()),
                'postalCode' => $postalCode,
            ],
            'partner' => $partner->getId(),
            'scheduledAt' => date('Y-m-d 23:59:00'),
            'items' => [
                [
                    'category' => $partnerCategory->getId(),
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

        $accessToken = $user->getAccessToken();

        $client->xmlHttpRequest('POST', "/api/v1/orders", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_Authorization' => $accessToken
        ], json_encode($content));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['price']), 'Missing price');

        $this->assertTrue(isset($content['status']), 'Missing status');
        $this->assertEquals(OrderStatus::FAILED, $content['status']);
    }

    /**
     * @medium
     */
    public function test_post_donation_is_created_without_payments()
    {
        $client = $this->createAuthorizedAdmin();

        $userService = $client->getContainer()->get(UserService::class);

        $partner = $this->createPartner($client->getContainer(), CategoryType::DONATION);

        $partnerCategory = $this->createPartnerCategory($client->getContainer(), $partner, CategoryType::DONATION);

        $client = $this->createUnauthorizedClient();

        $postalCode = $partner->getPostalCodes()->get(0)->getPostalCode();

        $content = [
            'location' => [
                'lat' => 12.12345,
                'lng' => 21.12345,
                'address' => md5(uniqid()),
                'postalCode' => $postalCode,
            ],
            'partner' => $partner->getId(),
            'scheduledAt' => date('Y-m-d 23:59:00'),
            'items' => [
                [
                    'category' => $partnerCategory->getId(),
                    'quantity' => 1
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

        $client->xmlHttpRequest('POST', "/api/v1/orders", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_Authorization' => $accessToken
        ], json_encode($content));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['status']), 'Missing status');
        $this->assertEquals(OrderStatus::CREATED, $content['status']);

        $this->assertFalse(isset($content['payments']), 'Invalid payments');
    }

    /**
     * @medium
     */
    public function test_post_order_with_country()
    {
        $client = $this->createAuthorizedAdmin();

        $userService = $client->getContainer()->get(UserService::class);

        $country = $this->createCountry($client->getContainer());
        $partner = $this->createPartner($client->getContainer(), CategoryType::DONATION);

        $partnerCategory = $this->createPartnerCategory($client->getContainer(), $partner, CategoryType::DONATION);

        $client = $this->createUnauthorizedClient();

        $postalCode = $partner->getPostalCodes()->get(0)->getPostalCode();
        $countryName = $country->getTranslations()->get(0)->getName();

        $content = [
            'location' => [
                'lat' => 12.12345,
                'lng' => 21.12345,
                'address' => md5(uniqid()),
                'postalCode' => $postalCode,
                'country' => $countryName
            ],
            'partner' => $partner->getId(),
            'scheduledAt' => date('Y-m-d 23:59:00'),
            'items' => [
                [
                    'category' => $partnerCategory->getId(),
                    'quantity' => 1
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

        $client->xmlHttpRequest('POST', "/api/v1/orders", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_Authorization' => $accessToken
        ], json_encode($content));

        $response = $client->getResponse();

        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());

        file_put_contents('/var/www/html/var/test.json', $response->getContent());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['status']), 'Missing status');
        $this->assertEquals(OrderStatus::CREATED, $content['status']);

        $this->assertTrue(isset($content['location']), 'Missing location');
        $this->assertTrue(isset($content['location']['country']['id']), 'Missing location.country.id');

        $this->assertEquals($country->getId(), $content['location']['country']['id'], 'Invalid location.country.id');

        $this->assertTrue(isset($content['location']['country']['name']), 'Missing location.country.name');
        $this->assertTrue(isset($content['location']['country']['locale']), 'Missing location.country.locale');
    }
}
