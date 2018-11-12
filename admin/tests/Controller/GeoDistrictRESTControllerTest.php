<?php

namespace App\Tests\Controller;

use App\Entity\CategoryType;
use App\Tests\Classes\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers \App\Controller\GeoDistrictRESTController
 */
class GeoDistrictRESTControllerTest extends WebTestCase
{

    /**
     * @small
     */
    public function test_gets_unauthorized()
    {
        $client = $this->createUnauthorizedClient();

        $client->request('GET', "/api/v2/geo/districts", [], [], [
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

        $client->request('GET', "/api/v2/geo/districts", [], [], [
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

        $client->request('GET', "/api/v2/geo/districts", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }
}