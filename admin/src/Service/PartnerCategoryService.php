<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Partner;
use App\Entity\PartnerCategory;
use JMS\Serializer\SerializationContext;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PartnerCategoryService
{

    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param Partner $partner
     * @param Category $category
     * @param $content
     *
     * @param bool $flush
     *
     * @return PartnerCategory
     * @throws \Exception
     */
    public function create(Partner $partner, Category $category, $content = null, $flush = true)
    {
        $entity = new PartnerCategory();
        $entity->setPartner($partner);
        $entity->setCategory($category);

        $this->update($entity, $content, $flush);

        return $entity;
    }

    /**
     * @param PartnerCategory $entity
     * @param $content
     *
     * @param bool $flush
     *
     * @throws \Exception
     */
    public function update(PartnerCategory $entity, $content = null, $flush = true)
    {
        $trans = $this->container->get('translator');
        $em = $this->container->get('doctrine')->getManager();
        $unitService = $this->container->get(UnitService::class);

        if (isset($content['price'])) {
            $entity->setPrice($content['price']);
        }

        if (isset($content['minAmount'])) {
            $entity->setMinAmount($content['minAmount']);
        }

        if (isset($content['unit'])) {
            $unit = $unitService->findOneByFilter([
                'id' => $content['unit']
            ]);
            if (!$unit) {
                throw new \Exception($trans->trans('validation.not_found'), 404);
            }

            $entity->setUnit($unit);
        }

        $match = $this->findOneByFilter([
            'minAmount' => $entity->getMinAmount(),
            'unit' => $entity->getUnit()->getId(),
            'partner' => $entity->getPartner()->getId(),
            'category' => $entity->getCategory()->getId()
        ]);
        if ($match && $match !== $entity) {
            throw new \Exception($trans->trans('validation.not_unique_partner_category'), 400);
        }

        $em->persist($entity);

        $flush && $em->flush();
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

        return $em->getRepository(PartnerCategory::class)->countByFilter($filter);
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

        return $em->getRepository(PartnerCategory::class)->findByFilter($filter, $page, $limit);
    }

    /**
     * @param array $filter
     *
     * @return PartnerCategory|null
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