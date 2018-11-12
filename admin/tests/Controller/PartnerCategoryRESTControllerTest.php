<?php

namespace App\Tests\Controller;

use App\Entity\CategoryType;
use App\Tests\Classes\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers \App\Controller\PartnerCategoryRESTController
 */
class PartnerCategoryRESTControllerTest extends WebTestCase
{

    public function getsProvider()
    {
        $client = $this->createUnauthorizedClient();

        $locales = explode('|', $client->getContainer()->getParameter('supported_locales'));
        $types = [CategoryType::JUNK_REMOVAL, CategoryType::RECYCLING, CategoryType::SHREDDING];

        $query = [];

        foreach ($locales as $locale) {
            foreach ($types as $type) {

                $filter = [
                    'filter' => [
                        'locale' => $locale,
                        'type' => $type
                    ]
                ];

                $query[] = [$filter];
                break;
            }
            break;
        }

        return $query;
    }

    /**
     * @dataProvider getsProvider
     *
     * @small
     *
     * @param $filter
     *
     * @throws \Exception
     */
    public function test_gets($filter)
    {
        $client = $this->createAuthorizedPartner();

        $client->request('GET', "/api/v2/partner-categories", $filter, [], [
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
    public function test_unauthorized()
    {
        $client = $this->createUnauthorizedClient();

        $client->request('GET', "/api/v2/partner-categories", [], [], [
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

        $client->request('GET', "/api/v2/partner-categories", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }
}