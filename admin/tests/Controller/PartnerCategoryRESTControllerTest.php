<?php

namespace App\Tests\Controller;

use App\Entity\CategoryType;
use App\Tests\Classes\PartnerCategoryCreator;
use App\Tests\Classes\PartnerCreator;
use App\Tests\Classes\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers \App\Controller\PartnerCategoryRESTController
 */
class PartnerCategoryRESTControllerTest extends WebTestCase
{

    use PartnerCreator;
    use PartnerCategoryCreator;

    public function getsProvider()
    {
        $client = $this->createUnauthorizedClient();

        $locales = explode('|', $client->getContainer()->getParameter('supported_locales'));
        $types = [CategoryType::JUNK_REMOVAL, CategoryType::RECYCLING, CategoryType::SHREDDING, CategoryType::DONATION];

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
    public function test_gets_unauthorized()
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
    public function test_gets_forbidden_no_partner()
    {
        $client = $this->createAuthorizedUser();

        $client->request('GET', "/api/v2/partner-categories", [], [], [
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

        $client->request('POST', "/api/v2/partner-categories", [], [], [
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

        $client->request('POST', "/api/v2/partner-categories", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    /**
     * @small
     */
    public function test_post()
    {
        $client = $this->createAuthorizedAdmin();

        $category = $this->createCategory($client->getContainer());
        $unit = $this->createUnit($client->getContainer());

        $client = $this->createAuthorizedPartner();

        $client->request('POST', "/api/v2/partner-categories", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ], json_encode([
            'category' => $category->getId(),
            'unit' => $unit->getId(),
            'minAmount' => rand(10, 1000),
            'price' => rand(10, 1000)
        ]));

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }
    /**
     * @small
     */
    public function test_post_failed_if_duplicate_created()
    {
        $client = $this->createAuthorizedAdmin();

        $partner = $this->createPartner($client->getContainer(), CategoryType::JUNK_REMOVAL);
        $partnerCategory = $this->createPartnerCategory($client->getContainer(), $partner);

        $client = $this->createAuthorizedClient($partner->getUser()->getUsername());

        $client->request('POST', "/api/v2/partner-categories", [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ], json_encode([
            'category' => $partnerCategory->getCategory()->getId(),
            'unit' => $partnerCategory->getUnit()->getId(),
            'minAmount' => $partnerCategory->getMinAmount(),
            'price' => $partnerCategory->getPrice()
        ]));

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
}