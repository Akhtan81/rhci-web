<?php

namespace App\Tests\Controller;

use App\Entity\PartnerSubscription;
use App\Entity\SubscriptionStatus;
use App\Service\PartnerSubscriptionService;
use App\Tests\Classes\PartnerCreator;
use App\Tests\Classes\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * CIS server does not use Stripe
 *
 * @covers \App\Controller\StripeWebhookRESTController
 *
 * @group ignore
 */
class StripeWebhookRESTControllerTest extends WebTestCase
{

    use PartnerCreator;

    /**
     * @medium
     */
    public function test_post()
    {
        $client = $this->createAuthorizedAdmin();

        $subscriptionService = $client->getContainer()->get(PartnerSubscriptionService::class);

        $partner = $this->createPartner($client->getContainer());

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
                        'customer' => $partner->getCustomerId(),
                        'subscription' => $id,
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

        $subscriptionService = $client->getContainer()->get(PartnerSubscriptionService::class);

        $partner = $this->createPartner($client->getContainer());

        $subscriptionService->create($partner);

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
                        'customer' => $partner->getCustomerId(),
                        'subscription' => $id,
                        'type' => 'subscription',
                        'period' => [
                            'start' => date('U') - 100000,
                            'end' => date('U') - 90000
                        ]
                    ]
                ]
            ]
        ]));

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $subscriptions = $subscriptionService->findByFilter([
            'partner' => $partner->getId()
        ]);

        /** @var PartnerSubscription $subscription */
        foreach ($subscriptions as $subscription) {
            switch ($subscription->getProviderId()) {
                case $id:
                    $this->assertEquals(SubscriptionStatus::COMPLETED, $subscription->getStatus());
                    break;
                default:
                    $this->assertEquals(SubscriptionStatus::ACTIVE, $subscription->getStatus());
            }
        }
    }

}