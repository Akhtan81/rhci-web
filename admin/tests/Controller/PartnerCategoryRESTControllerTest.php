<?php

namespace App\Tests\Controller;

use App\Entity\CategoryType;
use App\Entity\CountryTranslation;
use App\Entity\PartnerPostalCode;
use App\Entity\PartnerStatus;
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

        $client->xmlHttpRequest('GET', "/api/v2/partner-categories", $filter);

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

        $client->xmlHttpRequest('GET', "/api/v2/partner-categories");

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /**
     * @small
     */
    public function test_gets_forbidden_no_partner()
    {
        $client = $this->createAuthorizedUser();

        $client->xmlHttpRequest('GET', "/api/v2/partner-categories");

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    /**
     * @small
     */
    public function test_post_unauthorized()
    {
        $client = $this->createUnauthorizedClient();

        $client->xmlHttpRequest('POST', "/api/v2/partner-categories");

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /**
     * @small
     */
    public function test_post_forbidden_no_partner()
    {
        $client = $this->createAuthorizedUser();

        $client->xmlHttpRequest('POST', "/api/v2/partner-categories");

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

        $client->xmlHttpRequest('POST', "/api/v2/partner-categories", [], [], [], json_encode([
            'category' => $category->getId(),
            'unit' => $unit->getId(),
            'minAmount' => rand(10, 1000),
            'price' => rand(10, 1000)
        ]));

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public function test_gets_v1_returns_404_if_no_partners_found()
    {
        $client = $this->createUnauthorizedClient();

        $client->xmlHttpRequest('GET', "/api/v1/partner-categories", [
            'filter' => [
                'country' => md5(uniqid()),
                'postalCode' =>  md5(uniqid())
            ]
        ], [], [
            'HTTP_Accept-Language' => 'en'
        ]);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function test_gets_v1_bad_request_without_country()
    {
        $client = $this->createUnauthorizedClient();

        $client->xmlHttpRequest('GET', "/api/v1/partner-categories", [
            'filter' => [
//                'country' => md5(uniqid()),
                'postalCode' =>  md5(uniqid())
            ]
        ], [], [
            'HTTP_Accept-Language' => 'en'
        ]);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_gets_v1_bad_request_without_postalCode()
    {
        $client = $this->createUnauthorizedClient();

        $client->xmlHttpRequest('GET', "/api/v1/partner-categories", [
            'filter' => [
                'country' => md5(uniqid()),
//                'postalCode' =>  md5(uniqid())
            ]
        ], [], [
            'HTTP_Accept-Language' => 'en'
        ]);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_gets_v1_bad_request_without_country_and_postalCode()
    {
        $client = $this->createUnauthorizedClient();

        $client->xmlHttpRequest('GET', "/api/v1/partner-categories", [
            'filter' => [
//                'country' => md5(uniqid()),
//                'postalCode' =>  md5(uniqid())
            ]
        ], [], [
            'HTTP_Accept-Language' => 'en'
        ]);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_gets_v1_with_locale_returns_404_if_no_partners_found()
    {
        $client = $this->createUnauthorizedClient();

        $client->xmlHttpRequest('GET', "/api/v1/en/partner-categories", [
            'filter' => [
                'country' => md5(uniqid()),
                'postalCode' =>  md5(uniqid())
            ]
        ]);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function test_gets_v1_ok()
    {
        $client = $this->createAuthorizedAdmin();
        $em = $client->getContainer()->get('doctrine')->getManager();

        $partner = $this->createPartner($client->getContainer());
        $partnerCategory = $this->createPartnerCategory($client->getContainer(), $partner);

        $postalCode = substr(md5(uniqid()), 0, 16);

        $partner->setStatus(PartnerStatus::APPROVED);
        $partner->setCanManageShreddingOrders(true);
        $partner->setCanManageDonationOrders(true);
        $partner->setCanManageJunkRemovalOrders(true);
        $partner->setCanManageRecyclingOrders(true);

        $code = new PartnerPostalCode();
        $code->setPartner($partner);
        $code->setPostalCode($postalCode);
        $code->setType($partnerCategory->getCategory()->getType());

        $em->persist($partner);
        $em->persist($code);
        $em->flush();

        $countryName = null;
        $locale = 'en';

        /** @var CountryTranslation $translation */
        foreach ($partner->getCountry()->getTranslations() as $translation) {
            if ($translation->getLocale() === $locale) {
                $countryName = $translation->getName();
                break;
            }
        }

        if (!$countryName) {
            self::fail('Missing country name');
        }

        $client = $this->createUnauthorizedClient();

        $client->xmlHttpRequest('GET', "/api/v1/partner-categories", [
            'filter' => [
                'country' => $countryName,
                'postalCode' => $postalCode
            ]
        ], [], [
            'HTTP_Accept-Language' => $locale
        ]);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function test_gets_v1_ok_with_locale()
    {
        $client = $this->createAuthorizedAdmin();
        $em = $client->getContainer()->get('doctrine')->getManager();

        $partner = $this->createPartner($client->getContainer());
        $partnerCategory = $this->createPartnerCategory($client->getContainer(), $partner);

        $postalCode = substr(md5(uniqid()), 0, 16);

        $partner->setStatus(PartnerStatus::APPROVED);
        $partner->setCanManageShreddingOrders(true);
        $partner->setCanManageDonationOrders(true);
        $partner->setCanManageJunkRemovalOrders(true);
        $partner->setCanManageRecyclingOrders(true);

        $code = new PartnerPostalCode();
        $code->setPartner($partner);
        $code->setPostalCode($postalCode);
        $code->setType($partnerCategory->getCategory()->getType());

        $em->persist($partner);
        $em->persist($code);
        $em->flush();

        $countryName = null;
        $locale = 'en';

        /** @var CountryTranslation $translation */
        foreach ($partner->getCountry()->getTranslations() as $translation) {
            if ($translation->getLocale() === $locale) {
                $countryName = $translation->getName();
                break;
            }
        }

        if (!$countryName) {
            self::fail('Missing country name');
        }

        $client = $this->createUnauthorizedClient();

        $client->xmlHttpRequest('GET', "/api/v1/$locale/partner-categories", [
            'filter' => [
                'country' => $countryName,
                'postalCode' => $postalCode
            ]
        ]);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function test_gets_v1_ok_when_partner_has_two_postal_codes_for_junk_removal_and_recycling_and_only_requested_one_is_returned_in_response()
    {
        $client = $this->createAuthorizedAdmin();
        $em = $client->getContainer()->get('doctrine')->getManager();

        $partner = $this->createPartner($client->getContainer());

        $partnerCategory1 = $this->createPartnerCategory($client->getContainer(), $partner, CategoryType::JUNK_REMOVAL);
        $partnerCategory2 = $this->createPartnerCategory($client->getContainer(), $partner, CategoryType::RECYCLING);

        $postalCode1 = substr(md5(uniqid()), 0, 16);
        $postalCode2 = substr(md5(uniqid()), 0, 16);

        $partner->setStatus(PartnerStatus::APPROVED);
        $partner->setCanManageShreddingOrders(true);
        $partner->setCanManageDonationOrders(true);
        $partner->setCanManageJunkRemovalOrders(true);
        $partner->setCanManageRecyclingOrders(true);

        $code1 = new PartnerPostalCode();
        $code1->setPartner($partner);
        $code1->setPostalCode($postalCode1);
        $code1->setType($partnerCategory1->getCategory()->getType());

        $code2 = new PartnerPostalCode();
        $code2->setPartner($partner);
        $code2->setPostalCode($postalCode2);
        $code2->setType($partnerCategory2->getCategory()->getType());

        $em->persist($partner);
        $em->persist($code1);
        $em->persist($code2);
        $em->flush();

        $countryName = null;
        $locale = 'en';

        /** @var CountryTranslation $translation */
        foreach ($partner->getCountry()->getTranslations() as $translation) {
            if ($translation->getLocale() === $locale) {
                $countryName = $translation->getName();
                break;
            }
        }

        if (!$countryName) {
            self::fail('Missing country name');
        }

        $client = $this->createUnauthorizedClient();

        $client->xmlHttpRequest('GET', "/api/v1/en/partner-categories", [
            'filter' => [
                'country' => $countryName,
                'postalCode' => $postalCode1
            ]
        ]);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['count']), 'Missing count');
        $this->assertTrue(isset($content['items']), 'Missing items');
        $this->assertEquals(1, $content['count'], 'Invalid count');

        $partnerContent = $content['items'][0];

        $this->assertTrue(isset($partnerContent['partner']), 'Missing items.partner');
        $this->assertTrue(isset($partnerContent['categories']), 'Missing items.categories');

        $this->assertEquals(1, count($partnerContent['categories']), 'Invalid items.categories');

        $this->assertEquals($partner->getId(), $partnerContent['partner']['id'], 'Invalid partner.id');

        $categoryContent = $partnerContent['categories'][0];

        $this->assertTrue(isset($categoryContent['unit']), 'Missing items.categories.unit');
        $this->assertTrue(isset($categoryContent['minAmount']), 'Missing items.categories.minAmount');
        $this->assertTrue(isset($categoryContent['category']), 'Missing items.categories.category');
        $this->assertTrue(isset($categoryContent['category']['type']), 'Missing items.categories.category.type');
        $this->assertTrue(isset($categoryContent['category']['type']['key']), 'Missing items.categories.category.type.key');
        $this->assertTrue(isset($categoryContent['price']), 'Missing items.categories.price');

        $this->assertEquals($partnerCategory1->getCategory()->getType(), $categoryContent['category']['type']['key'], 'Invalid items.categories.category.type.key');
    }
}