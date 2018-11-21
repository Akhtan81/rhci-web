<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\CategoryType;
use App\Entity\Unit;
use JMS\Serializer\SerializationContext;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UnitService
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
     * @return Unit
     * @throws \Exception
     */
    public function create($content)
    {

        $entity = new Unit();

        $this->update($entity, $content);

        return $entity;
    }

    /**
     * @param Unit $entity
     * @param $content
     *
     * @throws \Exception
     */
    public function update(Unit $entity, $content, $flush = true)
    {
        $trans = $this->container->get('translator');

        $em = $this->container->get('doctrine')->getManager();


        if (isset($content['locale'])) {
            $entity->setLocale(trim($content['locale']));
        }

        if (isset($content['name'])) {
            $entity->setName(trim($content['name']));
        }

        $match = $this->findOneByFilter([
            'locale' => $entity->getLocale(),
            'name' => $entity->getName(),
        ]);
        if ($match && $match !== $entity) {
            throw new \Exception($trans->trans('validation.non_unique_category'), 400);
        }

        $em->persist($entity);

        $flush && $em->flush();
    }

    /**
     * @param Unit $entity
     *
     * @throws \Exception
     */
    public function remove(Unit $entity)
    {
        $trans = $this->container->get('translator');
        $em = $this->container->get('doctrine')->getManager();

        $em->remove($entity);
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

        return $em->getRepository(Unit::class)->countByFilter($filter);
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

        return $em->getRepository(Unit::class)->findByFilter($filter, $page, $limit);
    }

    /**
     * @param array $filter
     *
     * @return Unit|null
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