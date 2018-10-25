<?php

namespace App\Tests\Controller;

use App\Entity\SubscriptionStatus;
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
        $client = $this->createAuthorizedPartner();

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
}