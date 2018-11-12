<?php

namespace App\Service;

use App\Entity\Partner;
use App\Entity\PartnerPostalCode;
use App\Entity\PartnerRequest;
use App\Entity\PartnerStatus;
use JMS\Serializer\SerializationContext;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PartnerService
{

    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param $content
     *
     * @param bool $fillCategories
     * @return Partner
     * @throws \Exception
     */
    public function create($content, $fillCategories = true)
    {
        $userService = $this->container->get(UserService::class);
        $trans = $this->container->get('translator');
        $em = $this->container->get('doctrine')->getManager();

        $isAdmin = $userService->getAdmin();

        if (!isset($content['user'])) {
            throw new \Exception($trans->trans('validation.bad_request'), 400);
        }

        if (!$isAdmin && !isset($content['requestedPostalCodes'])) {
            throw new \Exception($trans->trans('validation.bad_request'), 400);
        }

        $user = $userService->create($content['user'], false);

        unset($content['user']);

        $entity = new Partner();
        $entity->setUser($user);

        if (!$isAdmin) {
            $user->setIsActive(false);

            $em->persist($user);
        }

        if (isset($content['requestedPostalCodes'])) {
            $this->handleRequestedCodes($entity, $content['requestedPostalCodes']);
        }

        $this->update($entity, $content, $fillCategories);

        return $entity;

    }

    /**
     * @param Partner $partner
     * @param $content
     *
     * @param bool $fillCategories
     * @throws \Exception
     */
    public function update(Partner $partner, $content, $fillCategories = true)
    {
        $em = $this->container->get('doctrine')->getManager();
        $trans = $this->container->get('translator');
        $defaultCountryName = $this->container->getParameter('default_country_name');
        $userService = $this->container->get(UserService::class);
        $countryService = $this->container->get(CountryService::class);
        $postalService = $this->container->get(PartnerPostalCodeService::class);
        $locationService = $this->container->get(LocationService::class);

        $isAdmin = $userService->getAdmin();
        $isApproved = false;

        $now = new \DateTime();

        if ($isAdmin && isset($content['status'])) {

            $isApproved = $partner->getStatus() !== PartnerStatus::APPROVED
                && $content['status'] === PartnerStatus::APPROVED;

            switch ($content['status']) {
                case PartnerStatus::CREATED:
                case PartnerStatus::REJECTED:
                case PartnerStatus::APPROVED:
                    $partner->setStatus($content['status']);
                    break;
                default:
                    throw new \Exception($trans->trans('validation.bad_request', 400));
            }
        }

        if (isset($content['provider'])) {
            $partner->setProvider($content['provider']);
        }

        if (isset($content['accountId'])) {
            $partner->setAccountId($content['accountId']);
        }

        if (isset($content['cardToken'])) {
            $this->onPartnerCardAdded($partner, $content['cardToken']);
        }

        if (isset($content['cardTokenResponse'])) {
            $partner->setCardTokenResponse($content['cardTokenResponse']);
        }

        if (isset($content['country'])) {
            $country = $countryService->findOneByFilter([
                'id' => $content['country']
            ]);
            if (!$country) {
                throw new \Exception($trans->trans('validation.not_found', 404));
            }

            $partner->setCountry($country);
        } else {
            $country = $countryService->findOneByFilter([
                'name' => $defaultCountryName
            ]);
            if (!$country) {
                throw new \Exception($trans->trans('validation.not_found', 404));
            }

            $partner->setCountry($country);
        }

        if (isset($content['postalCodes'])) {

            $partner->getPostalCodes()->clear();

            $codes = $postalService->findByFilter([
                'partner' => $partner->getId()
            ]);

            $codeRegistry = [];

            /** @var PartnerPostalCode $code */
            foreach ($codes as $code) {
                $code->setDeletedAt($now);

                $key = $code->getPostalCode() . $code->getType();

                $codeRegistry[$key] = $code;

                $em->persist($code);
            }

            foreach ($content['postalCodes'] as $item) {

                if (!(isset($item['postalCode']) && isset($item['type']))) {
                    throw new \Exception($trans->trans('validation.bad_request'), 400);
                }

                $postalCode = $item['postalCode'];
                $type = $item['type'];

                $key = $postalCode . $type;

                if (isset($codeRegistry[$key])) {
                    $code = $codeRegistry[$key];

                    $code->setDeletedAt(null);

                    $em->persist($code);

                } else {
                    $code = $postalService->create($partner, $postalCode, $type, false);
                }

                $partner->getPostalCodes()->add($code);
            }
        }

        if (isset($content['location'])) {
            $location = $locationService->create($content['location'], false);

            $partner->setLocation($location);
        }

        if (isset($content['user'])) {
            $userService->update($partner->getUser(), $content['user'], false);
        }

        $em->persist($partner);

        if ($isApproved && $fillCategories) {
            $this->onPartnerApproved($partner);
        }

        $this->createCustomer($partner);

        $em->persist($partner);
        $em->flush();
    }

    private function handleRequestedCodes(Partner $entity, $requestedPostalCodes)
    {
        $trans = $this->container->get('translator');
        $em = $this->container->get('doctrine')->getManager();

        foreach ($requestedPostalCodes as $item) {

            if (!(isset($item['postalCode']) && isset($item['type']))) {
                throw new \Exception($trans->trans('validation.bad_request'), 400);
            }

            $request = new PartnerRequest();
            $request->setPartner($entity);
            $request->setPostalCode($item['postalCode']);
            $request->setType($item['type']);

            $em->persist($request);

            $entity->addRequest($request);
        }
    }

    private function onPartnerApproved(Partner $partner)
    {
        $categoryService = $this->container->get(CategoryService::class);
        $partnerCategoryService = $this->container->get(PartnerCategoryService::class);

        $categories = $categoryService->findByFilter();
        foreach ($categories as $category) {
            $partnerCategoryService->create($partner, $category, false);
        }
    }

    public function createCustomer(Partner $partner)
    {
        $secret = $this->container->getParameter('stripe_client_secret');
        $trans = $this->container->get('translator');

        if ($partner->getCustomerId()) return;

        if ($secret) {
            \Stripe\Stripe::setApiKey($secret);

            try {
                $customer = \Stripe\Customer::create([
                    "email" => $partner->getUser()->getEmail(),
                ]);

                $response = json_encode($customer->jsonSerialize());

                $partner->setCustomerResponse($response);
                $partner->setCustomerId($customer->id);

            } catch (\Exception $e) {

                throw new \Exception($trans->trans('stripe.invalid_customer_from_partner', [
                    '__MSG__' => $e->getMessage()
                ]));
            }
        } else {
            $partner->setCustomerId("test");
        }
    }

    public function onPartnerCardAdded(Partner $partner, $token)
    {
        $secret = $this->container->getParameter('stripe_client_secret');
        $trans = $this->container->get('translator');

        if (!$partner->getCustomerId()) {
            $this->createCustomer($partner);
        }

        if ($partner->getCardToken() === $token) return;

        if ($secret) {

            $partner->setCardToken($token);

            \Stripe\Stripe::setApiKey($secret);

            try {
                $customer = \Stripe\Customer::retrieve($partner->getCustomerId());
                $customer->source = $partner->getCardToken();
                $customer->save();

                $response = json_encode($customer->jsonSerialize());
                $partner->setCustomerResponse($response);

            } catch (\Exception $e) {

                throw new \Exception($trans->trans('stripe.invalid_partner_card', [
                    '__MSG__' => $e->getMessage()
                ]));
            }
        } else {
            $partner->setCardToken("test");
        }
    }

    /**
     * @param array $filter
     *
     * @return int
     * @throws \Exception
     */
    public function countByFilter(array $filter = [])
    {
        $em = $this->container->get('doctrine')->getManager();

        return $em->getRepository(Partner::class)->countByFilter($filter);
    }

    /**
     * @param array $filter
     * @param int $page
     * @param int $limit
     *
     * @return array
     */
    public function findByFilter(array $filter = [], $page = 0, $limit = 0)
    {
        $em = $this->container->get('doctrine')->getManager();

        return $em->getRepository(Partner::class)->findByFilter($filter, $page, $limit);
    }

    /**
     * @param array $filter
     *
     * @return Partner|null
     */
    public function findOneByFilter(array $filter = [])
    {
        $items = $this->findByFilter($filter, 1, 1);
        if (count($items) !== 1) return null;

        return $items[0];
    }

    public function serialize($content, $groups = [])
    {
        return json_decode($this->container->get('jms_serializer')
            ->serialize($content, 'json', SerializationContext::create()
                ->setGroups(array_merge($groups, ['api_v1']))), true);
    }

    public function serializeV2($content)
    {
        return $this->serialize($content, ['api_v2']);
    }


}
