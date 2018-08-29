<?php

namespace App\Tests\Controller;

use App\Entity\CategoryType;
use App\Tests\Classes\WebTestCase;

/**
 * @covers \App\Controller\CategoryRESTController
 */
class CategoryRESTControllerTest extends WebTestCase
{

    public function getsProvider()
    {
        $client = $this->createUnauthorizedClient();

        $locales = explode('|', $client->getContainer()->getParameter('supported_locales'));

        $junk = [
            ['filter' => ['type' => CategoryType::JUNK_REMOVAL]]
        ];

        $recycling = [
            ['filter' => ['type' => CategoryType::RECYCLING]]
        ];

        $query = [];

        foreach ($locales as $locale) {
            $query[] = [
                $locale,
                $junk
            ];

            $query[] = [
                $locale,
                $recycling
            ];
        }

        return $query;
    }

    /**
     * @dataProvider getsProvider
     *
     * @param $locale
     * @param $filter
     */
    public function test_gets_if_authorized($locale, $filter)
    {
        $client = $this->createUnauthorizedClient();

        $client->request('GET', "/api/v1/$locale/order-categories", $filter, [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response = $client->getResponse();

        $this->assertTrue($response->isSuccessful());

        $content = json_decode($response->getContent(), true);
        $this->assertTrue(isset($content['items']), 'Missing items');
    }
}