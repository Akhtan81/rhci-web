<?php

namespace App\Tests\Controller;

use App\Entity\Country;
use App\Entity\Partner;
use App\Service\PartnerService;
use App\Tests\Classes\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers \App\Controller\PartnerRESTController
 */
class PartnerRESTControllerTest extends WebTestCase
{

    public function test_gets_unauthorized()
    {
        $client = $this->createUnauthorizedClient();

        $client->request('GET', "/api/v2/partners", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function test_gets_forbidden_partner()
    {
        $client = $this->createAuthorizedPartner();

        $client->request('GET', "/api/v2/partners", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function test_gets_admin()
    {
        $client = $this->createAuthorizedAdmin();

        $client->request('GET', "/api/v2/partners", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function test_get_unauthorized()
    {
        $client = $this->createUnauthorizedClient();

        $client->request('GET', "/api/v2/partners/1", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function test_get_forbidden_partner()
    {
        $client = $this->createAuthorizedPartner();

        $client->request('GET', "/api/v2/partners/1", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function test_get_admin()
    {
        $client = $this->createAuthorizedAdmin();
        $partnerService = $client->getContainer()->get(PartnerService::class);
        $em = $client->getContainer()->get('doctrine')->getManager();

        $country = $em->getRepository(Country::class)->findOneBy([]);
        if (!$country) {
            $this->fail('Country was not found');
        }

        $partner = $partnerService->create([
            'country' => $country->getId(),
            'postalCodes' => [mt_rand(10000, 99999), mt_rand(10000, 99999)],
            'user' => [
                'name' => md5(uniqid()),
                'email' => md5(uniqid()) . '@mail.com',
                'password' => '12345',
            ]
        ]);

        $client->request('GET', "/api/v2/partners/" . $partner->getId(), [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function test_post_unauthorized()
    {
        $client = $this->createUnauthorizedClient();

        $client->request('POST', "/api/v2/partners", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ], json_encode([ ]));

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

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

    public function test_post_admin()
    {
        $client = $this->createAuthorizedAdmin();
        $em = $client->getContainer()->get('doctrine')->getManager();

        $country = $em->getRepository(Country::class)->findOneBy([]);
        if (!$country) {
            $this->fail('Country was not found');
        }

        $client->request('POST', "/api/v2/partners", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ], json_encode([
            'country' => $country->getId(),
            'postalCodes' => [mt_rand(10000, 99999), mt_rand(10000, 99999)],
            'user' => [
                'name' => md5(uniqid()),
                'email' => md5(uniqid()) . '@mail.com',
                'password' => '12345',
            ]
        ]));

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

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

    public function test_put_admin()
    {
        $client = $this->createAuthorizedAdmin();
        $em = $client->getContainer()->get('doctrine')->getManager();
        $partnerService = $client->getContainer()->get(PartnerService::class);

        $country = $em->getRepository(Country::class)->findOneBy([]);
        if (!$country) {
            $this->fail('Country was not found');
        }

        $partner = $partnerService->create([
            'country' => $country->getId(),
            'postalCodes' => [mt_rand(10000, 99999)],
            'user' => [
                'name' => md5(uniqid()),
                'email' => md5(uniqid()) . '@mail.com',
                'password' => '12345',
            ]
        ]);

        $client->request('PUT', "/api/v2/partners/" . $partner->getId(), [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ], json_encode([
            'postalCodes' => [mt_rand(10000, 99999)],
            'user' => [
                'name' => md5(uniqid()),
            ]
        ]));

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }
}