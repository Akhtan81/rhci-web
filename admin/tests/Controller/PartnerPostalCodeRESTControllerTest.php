<?php

namespace App\Tests\Controller;

use App\Entity\CategoryType;
use App\Tests\Classes\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers \App\Controller\PartnerPostalCodeRESTController
 */
class PartnerPostalCodeRESTControllerTest extends WebTestCase
{

    /**
     * @small
     */
    public function test_find_owners()
    {
        $client = $this->createAuthorizedPartner();

        $client->request('POST', "/api/v2/partner-postal-codes/owners", [], [], [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ], json_encode([
            'postalCodes' => [
                [
                    'postalCode' => '00001',
                    'type' => CategoryType::RECYCLING
                ]
            ]
        ]));

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['postalCodes']), 'Missing postalCodes');
        $this->assertEquals(1, count($content['postalCodes']), 'Invalid postalCodes');
    }

    /**
     * @small
     */
    public function test_find_owners_unauthorized()
    {
        $client = $this->createUnauthorizedClient();

        $client->request('POST', "/api/v2/partner-postal-codes/owners", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /**
     * @small
     */
    public function test_find_owners_forbidden_user()
    {
        $client = $this->createAuthorizedUser();

        $client->request('POST', "/api/v2/partner-postal-codes/owners", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }
}
