<?php

namespace App\Tests\Controller;

use App\Entity\CategoryType;
use App\Tests\Classes\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers \App\Controller\GeoCountryRESTController
 */
class GeoCountryRESTControllerTest extends WebTestCase
{

    public function test_gets_unauthorized()
    {
        $client = $this->createUnauthorizedClient();

        $client->request('GET', "/api/v2/geo/countries");

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function test_gets_forbidden_partner()
    {
        $client = $this->createAuthorizedPartner();

        $client->request('GET', "/api/v2/geo/countries");

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function test_gets_admin()
    {
        $client = $this->createAuthorizedAdmin();

        $client->request('GET', "/api/v2/geo/countries");

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }
}