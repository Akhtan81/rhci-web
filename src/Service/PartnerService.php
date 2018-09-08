<?php

namespace App\Service;

use App\Entity\Partner;
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
        $trans = $this->container->get('translator');
        $em = $this->container->get('doctrine')->getManager();
        $userService = $this->container->get(UserService::class);
        $districtService = $this->container->get(DistrictService::class);

        if (isset($content['district'])) {
            $district = $districtService->findOneByFilter([
                'id' => $content['district']
            ]);
            if (!$district) {
                throw new \Exception($trans->trans('validation.not_found'), 404);
            }

            $match = $this->findOneByFilter([
                'district' => $district->getId()
            ]);
            if ($match && $match !== $partner) {
                throw new \Exception($trans->trans('validation.non_unique_partner_district'), 400);
            }

            $partner->setDistrict($district);
        }

        if (isset($content['user'])) {
            $userService->update($partner->getUser(), $content['user'], false);
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

    public function serialize($content)
    {
        return json_decode($this->container->get('jms_serializer')
            ->serialize($content, 'json', SerializationContext::create()
                ->setGroups(['api_v1'])), true);
    }

    public function serializeV2($content)
    {
        return json_decode($this->container->get('jms_serializer')
            ->serialize($content, 'json', SerializationContext::create()
                ->setGroups(['api_v1', 'api_v2'])), true);
    }


}