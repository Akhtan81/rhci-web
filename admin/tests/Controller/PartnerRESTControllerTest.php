<?php

namespace App\Tests\Controller;

use App\Entity\CategoryType;
use App\Entity\PartnerStatus;
use App\Service\PartnerService;
use App\Tests\Classes\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers \App\Controller\PartnerRESTController
 */
class PartnerRESTControllerTest extends WebTestCase
{

    /**
     * @small
     */
    public function test_gets_unauthorized()
    {
        $client = $this->createUnauthorizedClient();

        $client->request('GET', "/api/v2/partners", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /**
     * @small
     */
    public function test_gets_forbidden_partner()
    {
        $client = $this->createAuthorizedPartner();

        $client->request('GET', "/api/v2/partners", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    /**
     * @small
     */
    public function test_gets_admin()
    {
        $client = $this->createAuthorizedAdmin();

        $client->request('GET', "/api/v2/partners", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @small
     */
    public function test_get_unauthorized()
    {
        $client = $this->createUnauthorizedClient();

        $client->request('GET', "/api/v2/partners/1", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /**
     * @small
     */
    public function test_get_forbidden_partner()
    {
        $client = $this->createAuthorizedPartner();

        $client->request('GET', "/api/v2/partners/1", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    /**
     * @small
     */
    public function test_get_admin()
    {
        $client = $this->createAuthorizedAdmin();
        $partnerService = $client->getContainer()->get(PartnerService::class);

        $partner = $partnerService->create([
            'postalCodes' => [
                [
                    'postalCode' => mt_rand(10000, 99999),
                    'type' => CategoryType::JUNK_REMOVAL
                ],
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
        ], false);

        $client->request('GET', "/api/v2/partners/" . $partner->getId(), [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @small
     */
    public function test_post_unauthorized()
    {
        $client = $this->createUnauthorizedClient();

        $client->request('POST', "/api/v2/partners", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ], json_encode([]));

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /**
     * @small
     */
    public function test_post_forbidden_partner()
    {
        $client = $this->createAuthorizedPartner();

        $client->request('POST', "/api/v2/partners", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ], json_encode([]));

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    /**
     * @medium
     */
    public function test_post_admin()
    {
        $client = $this->createAuthorizedAdmin();

        $client->request('POST', "/api/v2/partners", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ], json_encode([
            'postalCodes' => [
                [
                    'postalCode' => mt_rand(10000, 99999),
                    'type' => CategoryType::JUNK_REMOVAL
                ],
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
                'postalCode' => md5(uniqid()),
            ]
        ]));

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    /**
     * @medium
     */
    public function test_put_unauthorized()
    {
        $client = $this->createUnauthorizedClient();

        $client->request('PUT', "/api/v2/partners/1", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ], json_encode([]));

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /**
     * @medium
     */
    public function test_put_forbidden_partner()
    {
        $client = $this->createAuthorizedPartner();

        $client->request('PUT', "/api/v2/partners/1", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ], json_encode([]));

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    /**
     * @medium
     */
    public function test_put_admin()
    {
        $client = $this->createAuthorizedAdmin();
        $partnerService = $client->getContainer()->get(PartnerService::class);

        $partner = $partnerService->create([
            'postalCodes' => [
                [
                    'postalCode' => mt_rand(10000, 99999),
                    'type' => CategoryType::JUNK_REMOVAL
                ],
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
        ], false);

        $client->request('PUT', "/api/v2/partners/" . $partner->getId(), [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ], json_encode([
            'postalCodes' => [
                [
                    'postalCode' => mt_rand(10000, 99999),
                    'type' => CategoryType::RECYCLING
                ],
            ],
            'user' => [
                'name' => md5(uniqid()),
            ]
        ]));

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @medium
     */
    public function test_post_signup_unauthorized()
    {
        $client = $this->createUnauthorizedClient();

        $client->request('POST', "/api/v1/partners/signup", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ], json_encode([
            'requestedPostalCodes' => [
                [
                    'postalCode' => mt_rand(10000, 99999),
                    'type' => CategoryType::JUNK_REMOVAL
                ],
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
        ]));

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['status']), 'Missing status');
        $this->assertTrue(isset($content['user']), 'Missing user');
        $this->assertTrue(isset($content['user']['isActive']), 'Missing user.isActive');

        $this->assertEquals(PartnerStatus::CREATED, $content['status']);
        $this->assertFalse($content['user']['isActive']);
    }

    /**
     * @medium
     */
    public function test_post_signup_unauthorized_without_postal_code()
    {
        $client = $this->createUnauthorizedClient();

        $client->request('POST', "/api/v1/partners/signup", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ], json_encode([
            'requestedPostalCodes' => [
                [
                    'postalCode' => mt_rand(10000, 99999),
                    'type' => CategoryType::JUNK_REMOVAL
                ],
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
        ]));

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['status']), 'Missing status');
        $this->assertTrue(isset($content['user']), 'Missing user');
        $this->assertTrue(isset($content['user']['isActive']), 'Missing user.isActive');

        $this->assertEquals(PartnerStatus::CREATED, $content['status']);
        $this->assertFalse($content['user']['isActive']);
    }

    /**
     * @medium
     */
    public function test_put_rejected_without_postal_codes_is_allowed()
    {
        $client = $this->createAuthorizedAdmin();
        $partnerService = $client->getContainer()->get(PartnerService::class);

        $partner = $partnerService->create([
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
        ], false);

        $client->request('PUT', "/api/v2/partners/" . $partner->getId(), [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ], json_encode([
            'status' => 'rejected'
        ]));

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @medium
     */
    public function test_put_rejected_active_partner()
    {
        $client = $this->createAuthorizedAdmin();
        $partnerService = $client->getContainer()->get(PartnerService::class);

        $partner = $partnerService->create([
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
        ], false);

        $client->request('PUT', "/api/v2/partners/" . $partner->getId(), [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ], json_encode([
            'status' => PartnerStatus::REJECTED
        ]));

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }
}
