<?php

namespace App\Service;

use App\Entity\OrderItem;
use JMS\Serializer\SerializationContext;
use Symfony\Component\DependencyInjection\ContainerInterface;

class OrderItemService
{

    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
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

        return $em->getRepository(OrderItem::class)->countByFilter($filter);
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

        return $em->getRepository(OrderItem::class)->findByFilter($filter, $page, $limit);
    }

    /**
     * @param array $filter
     *
     * @return OrderItem|null
     */
    public function findOneByFilter(array $filter = [])
    {
        $items = $this->findByFilter($filter, 1, 1);
        if (count($items) !== 1) return null;

        return $items[0];
    }

    /**
     * @param $content
     * @param array $groups
     *
     * @return array
     */
    public function serialize($content, $groups = [])
    {
        $result = json_decode($this->container->get('jms_serializer')
            ->serialize($content, 'json', SerializationContext::create()
                ->setGroups(array_merge(['api_v1'], $groups))), true);

        return $result;
    }

    /**
     * @param $content
     *
     * @return array
     */
    public function serializeV2($content)
    {
        return $this->serialize($content, ['api_v2']);
    }

}
