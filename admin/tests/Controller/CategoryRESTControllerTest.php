<?php

namespace App\Tests\Controller;

use App\Entity\CategoryType;
use App\Service\LocaleService;
use App\Tests\Classes\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers \App\Controller\CategoryRESTController
 */
class CategoryRESTControllerTest extends WebTestCase
{

    public function getsProvider()
    {
        $client = $this->createUnauthorizedClient();

        $locales = $client->getContainer()->get(LocaleService::class)->getSupportedLocales();
        $types = [CategoryType::JUNK_REMOVAL, CategoryType::RECYCLING, CategoryType::SHREDDING, CategoryType::DONATION];

        $query = [];

        foreach ($locales as $locale) {
            foreach ($types as $type) {

                $filter = [
                    'filter' => [
                        'type' => $type
                    ]
                ];

                $query[] = [$locale, $filter];
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
     * @param $locale
     * @param $filter
     */
    public function test_gets_v1($locale, $filter)
    {
        $client = $this->createUnauthorizedClient();

        $client->xmlHttpRequest('GET', "/api/v1/order-categories", $filter, [], [
            'HTTP_Accept-Language' => $locale,
        ]);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertTrue(isset($content['items']), 'Missing items');
    }

    /**
     * @dataProvider getsProvider
     *
     * @small
     *
     * @param $locale
     * @param $filter
     */
    public function test_gets_v1_with_locale($locale, $filter)
    {
        $client = $this->createUnauthorizedClient();

        $client->xmlHttpRequest('GET', "/api/v1/$locale/order-categories", $filter);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertTrue(isset($content['items']), 'Missing items');
    }

    /**
     * @dataProvider getsProvider
     *
     * @small
     *
     * @param $locale
     * @param $filter
     */
    public function test_gets_v2($locale, $filter)
    {
        $client = $this->createAuthorizedAdmin();

        $filter['filter']['locale'] = $locale;

        $client->xmlHttpRequest('GET', "/api/v2/order-categories", $filter);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertTrue(isset($content['items']), 'Missing items');
    }

    /**
     * @dataProvider getsProvider
     *
     * @small
     *
     * @param $locale
     * @param $filter
     */
    public function test_gets_v2_partner($locale, $filter)
    {
        $client = $this->createAuthorizedPartner();

        $filter['filter']['locale'] = $locale;

        $client->xmlHttpRequest('GET', "/api/v2/order-categories", $filter);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertTrue(isset($content['items']), 'Missing items');
    }

    /**
     * @small
     */
    public function test_gets_v2_unauthorized()
    {
        $client = $this->createUnauthorizedClient();

        $client->xmlHttpRequest('GET', "/api/v2/order-categories");

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /**
     * @small
     */
    public function test_gets_v2_forbidden_user()
    {
        $client = $this->createAuthorizedUser();

        $client->xmlHttpRequest('GET', "/api/v2/order-categories");

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }
}