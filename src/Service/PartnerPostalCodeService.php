<?php

namespace App\Service;

use App\Entity\Partner;
use App\Entity\PartnerPostalCode;
use JMS\Serializer\SerializationContext;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PartnerPostalCodeService
{

    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param Partner $partner
     * @param $postalCode
     * @param $type
     * @param bool $flush
     *
     * @return PartnerPostalCode
     * @throws \Exception
     */
    public function create(Partner $partner, $postalCode, $type, $flush = true)
    {
        $em = $this->container->get('doctrine')->getManager();
        $trans = $this->container->get('translator');

        $entity = $this->findOneByFilter([
            'postalCode' => $postalCode,
            'type' => $type,
        ]);
        if ($entity) {

            if ($entity->getPartner() !== $partner) {
                throw new \Exception($trans->trans('validation.non_unique_partner_postal_code'), 400);
            }

            return $entity;
        }

        $entity = new PartnerPostalCode();
        $entity->setPartner($partner);
        $entity->setPostalCode($postalCode);
        $entity->setType($type);

        $em->persist($entity);

        $flush && $em->flush();

        return $entity;
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

        return $em->getRepository(PartnerPostalCode::class)->countByFilter($filter);
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

        return $em->getRepository(PartnerPostalCode::class)->findByFilter($filter, $page, $limit);
    }

    /**
     * @param array $filter
     *
     * @return PartnerPostalCode|null
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
}