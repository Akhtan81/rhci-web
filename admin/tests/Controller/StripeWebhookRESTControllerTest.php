<?php

namespace App\Tests\Controller;

use App\Entity\PartnerSubscription;
use App\Entity\SubscriptionStatus;
use App\Service\PartnerService;
use App\Service\PartnerSubscriptionService;
use App\Tests\Classes\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers \App\Controller\StripeWebhookRESTController
 */
class StripeWebhookRESTControllerTest extends WebTestCase
{

    /**
     * @medium
     */
    public function test_post()
    {
        $client = $this->createAuthorizedAdmin();

        $partnerService = $client->getContainer()->get(PartnerService::class);
        $subscriptionService = $client->getContainer()->get(PartnerSubscriptionService::class);

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

        $id = md5(uniqid());

        $client = $this->createUnauthorizedClient();

        $client->request('POST', "/api/webhooks/stripe", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'HTTP_Content-Type' => 'application/json',
        ], json_encode([
            'id' => md5(uniqid()),
            'object' => 'invoice',
            'customer' => $partner->getCustomerId(),
            'lines' => [
                'data' => [
                    [
                        'id' => $id,
                        'type' => 'subscription',
                        'period' => [
                            'start' => date('U') - 1000,
                            'end' => date('U') + 100
                        ]
                    ]
                ]
            ]
        ]));

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $subscriptions = $subscriptionService->findByFilter([
            'partner' => $partner->getId()
        ], 1, 1);

        $this->assertEquals(1, count($subscriptions));

        /** @var PartnerSubscription $subscription */
        $subscription = $subscriptions[0];

        $this->assertEquals($id, $subscription->getProviderId());
        $this->assertEquals(SubscriptionStatus::ACTIVE, $subscription->getStatus());
    }

    /**
     * @medium
     */
    public function test_post_old_subscription_should_be_completed()
    {
        $client = $this->createAuthorizedAdmin();

        $partnerService = $client->getContainer()->get(PartnerService::class);
        $subscriptionService = $client->getContainer()->get(PartnerSubscriptionService::class);

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

        $id = md5(uniqid());

        $client = $this->createUnauthorizedClient();

        $client->request('POST', "/api/webhooks/stripe", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
            'HTTP_Content-Type' => 'application/json',
        ], json_encode([
            'id' => md5(uniqid()),
            'object' => 'invoice',
            'customer' => $partner->getCustomerId(),
            'lines' => [
                'data' => [
                    [
                        'id' => md5(uniqid()),
                        'subscription' => $id,
                        'type' => 'subscription',
                        'period' => [
                            'start' => date('U') - 10000,
                            'end' => date('U') - 9000
                        ]
                    ]
                ]
            ]
        ]));

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $subscriptions = $subscriptionService->findByFilter([
            'partner' => $partner->getId()
        ], 1, 1);

        $this->assertEquals(1, count($subscriptions));

        /** @var PartnerSubscription $subscription */
        $subscription = $subscriptions[0];

        $this->assertEquals($id, $subscription->getProviderId());
        $this->assertEquals(SubscriptionStatus::COMPLETED, $subscription->getStatus());
    }

}