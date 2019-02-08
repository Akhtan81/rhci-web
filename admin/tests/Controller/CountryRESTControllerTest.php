<?php

namespace App\Tests\Controller;

use App\Service\LocaleService;
use App\Tests\Classes\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers \App\Controller\CountryRESTController
 */
class CountryRESTControllerTest extends WebTestCase
{

    public function getsProvider()
    {
        $client = $this->createUnauthorizedClient();

        $locales = $client->getContainer()->get(LocaleService::class)->getSupportedLocales();

        $query = [];

        foreach ($locales as $locale) {
            $query[] = [$locale];
        }

        return $query;
    }

    /**
     * @dataProvider getsProvider
     *
     * @small
     *
     * @param $locale
     */
    public function test_gets_v1($locale)
    {
        $client = $this->createUnauthorizedClient();

        $client->xmlHttpRequest('GET', "/api/v1/countries", [], [], [
            'HTTP_Accept-Language' => $locale,
        ]);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertTrue(isset($content['items']), 'Missing items');

        foreach ($content['items'] as $item) {
            $this->assertTrue(isset($item['currency']), 'Missing currency');
            $this->assertTrue(isset($item['name']), 'Missing name');
            $this->assertTrue(isset($item['locale']), 'Missing locale');
            $this->assertEquals($locale, $item['locale'], 'Invalid locale');
        }
    }

    /**
     * @dataProvider getsProvider
     *
     * @small
     *
     * @param $locale
     */
    public function test_gets_v1_with_locale($locale)
    {
        $client = $this->createUnauthorizedClient();

        $client->xmlHttpRequest('GET', "/api/v1/$locale/countries");

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertTrue(isset($content['items']), 'Missing items');

        foreach ($content['items'] as $item) {
            $this->assertTrue(isset($item['currency']), 'Missing currency');
            $this->assertTrue(isset($item['name']), 'Missing name');
            $this->assertTrue(isset($item['locale']), 'Missing locale');
            $this->assertEquals($locale, $item['locale'], 'Invalid locale');
        }
    }
}