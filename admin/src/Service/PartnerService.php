<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Partner;
use App\Entity\PartnerPostalCode;
use App\Entity\PartnerRequest;
use App\Entity\PartnerStatus;
use App\Entity\RequestedCategory;
use App\Entity\RequestedCategoryStatus;
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
     * @param bool $flush
     * @return Partner
     * @throws \Exception
     */
    public function create($content, $flush = true)
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
        $entity->setCanManageDonationOrders(true);
        $entity->setCanManageRecyclingOrders(true);
        $entity->setUser($user);

        if (!$isAdmin) {
            $user->setIsActive(false);

            $em->persist($user);
        }

        $this->update($entity, $content, $flush);

        return $entity;

    }

    /**
     * @param Partner $partner
     * @param $content
     *
     * @param bool $flush
     * @throws \Exception
     */
    public function update(Partner $partner, $content, $flush = true)
    {
        $em = $this->container->get('doctrine')->getManager();
        $trans = $this->container->get('translator');
        $locale = $this->container->getParameter('locale');
        $defaultCountryName = $this->container->getParameter('default_country_name');
        $userService = $this->container->get(UserService::class);
        $countryService = $this->container->get(CountryService::class);
        $postalService = $this->container->get(PartnerPostalCodeService::class);
        $locationService = $this->container->get(LocationService::class);
        $emailService = $this->container->get(EmailService::class);

        $isAdmin = $userService->getAdmin();
        $isApproved = false;
        $isRejected = false;

        $now = new \DateTime();

        if ($isAdmin) {

            if (isset($content['status'])) {

                $isApproved = $partner->getStatus() !== PartnerStatus::APPROVED
                    && $content['status'] === PartnerStatus::APPROVED;

                $isRejected = $partner->getStatus() !== PartnerStatus::REJECTED
                    && $content['status'] === PartnerStatus::REJECTED;

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
                'locale' => $locale,
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
            if (!$partner->getLocation()) {
                $location = $locationService->create($content['location'], false);

                $partner->setLocation($location);
            } else {
                $locationService->update($partner->getLocation(), $content['location'], false);
            }
        }

        if (isset($content['user'])) {
            $userService->update($partner->getUser(), $content['user'], false);
        }

        if (isset($content['requestedPostalCodes']) && count($content['requestedPostalCodes']) > 0) {
            $this->handleRequestedCodes($partner, $content['requestedPostalCodes']);
        }

        if (isset($content['requestedCategories']) && count($content['requestedCategories']) > 0) {
            $this->handleRequestedcategories($partner, $content['requestedCategories']);
        }

        $em->persist($partner);

        $this->createCustomer($partner);

        $em->persist($partner);

        $flush && $em->flush();

        if ($isApproved) {
            $emailService->onPartnerApproved($partner);
        }

        if ($isRejected) {
            $emailService->onPartnerRejected($partner);
        }
    }

    private function handleRequestedCategories(Partner $entity, $requestedCategories)
    {
        $trans = $this->container->get('translator');
        $em = $this->container->get('doctrine')->getManager();

        $ids = array_map(function ($item) {
            return $item['category'];
        }, $requestedCategories);

        if (!$ids) return;

        $categories = $em->getRepository(Category::class)->findBy([
            'id' => $ids
        ]);
        if (count($ids) !== count($categories)) {
            throw new \Exception($trans->trans('validation.not_found'), 404);
        }

        $categoryRegistry = [];
        /** @var Category $category */
        foreach ($categories as $category) {
            $categoryRegistry[$category->getId()] = $category;
        }

        $now = new \DateTime();

        foreach ($requestedCategories as $content) {

            $request = null;

            $category = $categoryRegistry[$content['category']];

            if ($entity->getId()) {
                /** @var RequestedCategory $request */
                $request = $em->getRepository(RequestedCategory::class)->findOneBy([
                    'partner' => $entity->getId(),
                    'category' => $category->getId(),
                ]);
            }

            if (!$request) {
                $request = new RequestedCategory();
                $request->setPartner($entity);
                $request->setCategory($category);

                $entity->addRequestedCategory($request);
            }

            $request->setUpdatedAt($now);

            if (isset($content['status'])) {

                switch ($request->getStatus()) {
                    case RequestedCategoryStatus::CREATED:
                    case RequestedCategoryStatus::REJECTED:
                    case RequestedCategoryStatus::APPROVED:
                        $request->setStatus($content['status']);
                        break;
                }
            }


            $em->persist($request);
        }
    }

    private function handleRequestedCodes(Partner $entity, $requestedPostalCodes)
    {
        $trans = $this->container->get('translator');
        $em = $this->container->get('doctrine')->getManager();

        foreach ($requestedPostalCodes as $item) {

            if (!(isset($item['postalCode']) && isset($item['type']))) {
                throw new \Exception($trans->trans('validation.bad_request'), 400);
            }

            $request = null;

            if ($entity->getId()) {
                $request = $em->getRepository(PartnerRequest::class)->findOneBy([
                    'partner' => $entity->getId(),
                    'type' => $item['type'],
                    'postalCode' => $item['postalCode'],
                ]);
            }

            if (!$request) {
                $request = new PartnerRequest();
                $request->setPartner($entity);
                $request->setPostalCode($item['postalCode']);
                $request->setType($item['type']);

                $em->persist($request);

                $entity->addRequest($request);
            }
        }
    }

    public function createCustomer(Partner $partner, $force = false)
    {
        $isEnabled = $this->container->getParameter('stripe_enabled');
        if (!$isEnabled) {

            $partner->setCanManageJunkRemovalOrders(true);
            $partner->setCanManageShreddingOrders(true);

            return;
        }

        $secret = $this->container->getParameter('stripe_client_secret');
        $trans = $this->container->get('translator');

        if (!$force && $partner->getCustomerId()) return;

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

        $partner->setCanManageJunkRemovalOrders(true);
        $partner->setCanManageShreddingOrders(true);
    }

    public function onPartnerCardAdded(Partner $partner, $token)
    {
        $isEnabled = $this->container->getParameter('stripe_enabled');
        if (!$isEnabled) {

            $partner->setCanManageRecyclingOrders(true);

            return;
        }

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

        $partner->setCanManageRecyclingOrders(true);
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

    public function serializeV2($content, $locale)
    {
        return $this->serialize($content, $locale, ['api_v2']);
    }

    public function serialize($content, $locale, $groups = [])
    {
        $groups[] = 'api_v1';

        $result = json_decode($this->container->get('jms_serializer')
            ->serialize($content, 'json', SerializationContext::create()
                ->setGroups($groups)), true);

        if ($content instanceof Partner) {
            $this->onPostSerialize($result, $locale);
        } else {
            foreach ($result as &$item) {
                $this->onPostSerialize($item, $locale);
            }
        }
        return $result;
    }

    public function onPostSerialize(&$content, $locale)
    {
        $countryService = $this->container->get(CountryService::class);

        if (isset($content['country'])) {
            $countryService->onPostSerialize($content['country'], $locale);
        }
    }
}
