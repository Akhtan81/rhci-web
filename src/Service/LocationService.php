<?php

namespace App\Service;

use App\Entity\Location;
use JMS\Serializer\SerializationContext;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LocationService
{

    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function create($content, $flush = true)
    {
        $location = new Location();

        $this->update($location, $content, $flush);

        return $location;
    }

    public function update(Location $location, $content, $flush = true)
    {
        $em = $this->container->get('doctrine')->getManager();

        if (isset($content['postalCode'])) {
            $location->setPostalCode($content['postalCode']);
        }

        if (isset($content['lng'])) {
            $location->setLng($content['lng']);
        }

        if (isset($content['lat'])) {
            $location->setLat($content['lat']);
        }

        if (isset($content['address'])) {
            $location->setAddress($content['address']);
        }

        $em->persist($location);

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

        return $em->getRepository(Location::class)->countByFilter($filter);
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

        return $em->getRepository(Location::class)->findByFilter($filter, $page, $limit);
    }

    /**
     * @param array $filter
     *
     * @return null|Location
     */
    public function findOneByFilter(array $filter)
    {
        $items = $this->findByFilter($filter, 1, 1);
        if (count($items) !== 1) return null;

        return $items[0];
    }

    public function serialize($content)
    {
        return json_decode($this->container->get('jms_serializer')
            ->serialize($content, 'json', SerializationContext::create()
                ->setGroups(['api_v1', 'api_v1_user'])), true);
    }


}
