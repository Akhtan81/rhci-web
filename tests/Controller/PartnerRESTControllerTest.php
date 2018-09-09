<?php

namespace App\Tests\Controller;

use App\Entity\City;
use App\Entity\Partner;
use App\Service\DistrictService;
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
        $em = $client->getContainer()->get('doctrine')->getManager();

        $partner = $em->getRepository(Partner::class)->findOneBy([]);
        if (!$partner) {
            $this->fail('Partner was not found');
        }

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
        ], json_encode([
            'district' => 1,
            'user' => [
                'name' => md5(uniqid()),
                'email' => md5(uniqid()) . '@mail.com',
                'password' => '12345',
            ]
        ]));

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function test_post_forbidden_partner()
    {
        $client = $this->createAuthorizedPartner();

        $client->request('POST', "/api/v2/partners", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ], json_encode([
            'district' => 1,
            'user' => [
                'name' => md5(uniqid()),
                'email' => md5(uniqid()) . '@mail.com',
                'password' => '12345',
            ]
        ]));

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function test_post_admin()
    {
        $client = $this->createAuthorizedAdmin();
        $em = $client->getContainer()->get('doctrine')->getManager();
        $districtService = $client->getContainer()->get(DistrictService::class);

        $city = $em->getRepository(City::class)->findOneBy([]);
        if (!$city) {
            $this->fail('City was not found');
        }

        $partner = $em->getRepository(Partner::class)->findOneBy([]);
        if (!$partner) {
            $this->fail('Partner was not found');
        }

        $district = $districtService->create([
            'name' => md5(uniqid()),
            'city' => $city->getId(),
            'postalCode' => substr(md5(uniqid()), 0, 7)
        ]);

        $client->request('POST', "/api/v2/partners", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ], json_encode([
            'district' => $district->getId(),
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
        ], json_encode([
            'district' => 1,
            'user' => [
                'name' => md5(uniqid()),
            ]
        ]));

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function test_put_forbidden_partner()
    {
        $client = $this->createAuthorizedPartner();

        $client->request('PUT', "/api/v2/partners/1", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ], json_encode([
            'district' => 1,
            'user' => [
                'name' => md5(uniqid()),
            ]
        ]));

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function test_put_admin()
    {
        $client = $this->createAuthorizedAdmin();
        $em = $client->getContainer()->get('doctrine')->getManager();
        $districtService = $client->getContainer()->get(DistrictService::class);
        $partnerService = $client->getContainer()->get(PartnerService::class);

        $city = $em->getRepository(City::class)->findOneBy([]);
        if (!$city) {
            $this->fail('City was not found');
        }

        $partner = $em->getRepository(Partner::class)->findOneBy([]);
        if (!$partner) {
            $this->fail('Partner was not found');
        }

        $district1 = $districtService->create([
            'name' => md5(uniqid()),
            'city' => $city->getId(),
            'postalCode' => substr(md5(uniqid()), 0, 7)
        ]);

        $district2 = $districtService->create([
            'name' => md5(uniqid()),
            'city' => $city->getId(),
            'postalCode' => substr(md5(uniqid()), 0, 7)
        ]);

        $partner = $partnerService->create([
            'district' => $district1->getId(),
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
            'district' => $district2->getId(),
            'user' => [
                'name' => md5(uniqid()),
            ]
        ]));

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }
}