<?php

namespace App\Service;

use App\Entity\Partner;
use App\Entity\PartnerPostalCode;
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
     * @return Partner
     * @throws \Exception
     */
    public function create($content)
    {
        $userService = $this->container->get(UserService::class);

        $user = $userService->create($content['user'], false);

        unset($content['user']);

        $entity = new Partner();
        $entity->setUser($user);

        if (isset($content['requestedPostalCodes'])) {
            $entity->setRequestedPostalCodes(implode(',', $content['requestedPostalCodes']));
        }

        $this->update($entity, $content);

        return $entity;

    }

    /**
     * @param Partner $partner
     * @param $content
     *
     * @throws \Exception
     */
    public function update(Partner $partner, $content)
    {
        $em = $this->container->get('doctrine')->getManager();
        $trans = $this->container->get('translator');
        $userService = $this->container->get(UserService::class);
        $countryService = $this->container->get(CountryService::class);
        $postalService = $this->container->get(PartnerPostalCodeService::class);


        $now = new \DateTime();

        if (isset($content['provider'])) {
            $partner->setProvider($content['provider']);
        }

        if (isset($content['accountId'])) {
            $partner->setAccountId($content['accountId']);
        }

        if (isset($content['country'])) {
            $country = $countryService->findOneByFilter([
                'id' => $content['country']
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

                $codeRegistry[$code->getPostalCode()] = $code;

                $em->persist($code);
            }

            foreach ($content['postalCodes'] as $item) {
                if (isset($codeRegistry[$item])) {
                    $code = $codeRegistry[$item];

                    $code->setDeletedAt(null);

                    $em->persist($code);

                } else {
                    $code = $postalService->create($partner, $item, false);
                }

                $partner->getPostalCodes()->add($code);
            }
        }

        if (isset($content['user'])) {
            $userService->update($partner->getUser(), $content['user'], false);
        }

        if ($partner->getPostalCodes()->count() === 0) {
            throw new \Exception($trans->trans('validation.partner_missing_request_codes'), 404);
        }

        $em->persist($partner);
        $em->flush();
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