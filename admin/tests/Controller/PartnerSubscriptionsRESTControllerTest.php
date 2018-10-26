<?php

namespace App\Tests\Controller;

use App\Entity\CategoryType;
use App\Entity\PartnerStatus;
use App\Entity\SubscriptionStatus;
use App\Service\PartnerService;
use App\Service\PartnerSubscriptionService;
use App\Tests\Classes\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers \App\Controller\PartnerSubscriptionsRESTController
 */
class PartnerSubscriptionsRESTControllerTest extends WebTestCase
{

    /**
     * @small
     */
    public function test_gets()
    {
        $client = $this->createAuthorizedPartner();

        $client->request('GET', "/api/v2/me/subscriptions", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertTrue(isset($content['items']), 'Missing items');
    }

    /**
     * @small
     */
    public function test_post()
    {
        $client = $this->createAuthorizedAdmin();

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
                'lat' => 9.9999,
                'lng' => 1.1111,
                'address' => md5(uniqid()),
                'postalCode' => '00001'
            ]

        ], false);

        $client = $this->createAuthorizedClient($partner->getUser()->getUsername());

        $client->request('POST', "/api/v2/me/subscriptions", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['status']), 'Missing status');
        $this->assertEquals(SubscriptionStatus::ACTIVE, $content['status']);
    }

    /**
     * @small
     */
    public function test_post_cancel()
    {
        $client = $this->createAuthorizedAdmin();

        $partnerService = $client->getContainer()->get(PartnerService::class);
        $subscriptionService = $client->getContainer()->get(PartnerSubscriptionService::class);

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

        ], false);

        $subscriptionService->create($partner);

        $client = $this->createAuthorizedClient($partner->getUser()->getUsername());

        $client->request('POST', "/api/v2/me/subscriptions/cancel", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    /**
     * @small
     */
    public function test_unauthorized()
    {
        $client = $this->createUnauthorizedClient();

        $client->request('GET', "/api/v2/me/subscriptions", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /**
     * @small
     */
    public function test_forbidden_no_partner()
    {
        $client = $this->createAuthorizedUser();

        $client->request('GET', "/api/v2/me/subscriptions", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    /**
     * @small
     */
    public function test_post_unauthorized()
    {
        $client = $this->createUnauthorizedClient();

        $client->request('POST', "/api/v2/me/subscriptions", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /**
     * @small
     */
    public function test_post_forbidden_no_partner()
    {
        $client = $this->createAuthorizedUser();

        $client->request('POST', "/api/v2/me/subscriptions", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    /**
     * @small
     */
    public function test_post_cancel_unauthorized()
    {
        $client = $this->createUnauthorizedClient();

        $client->request('POST', "/api/v2/me/subscriptions/cancel", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /**
     * @small
     */
    public function test_post_cancel_forbidden_no_partner()
    {
        $client = $this->createAuthorizedUser();

        $client->request('POST', "/api/v2/me/subscriptions/cancel", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }
}