<?php

namespace App\Service;

use App\Entity\Location;
use App\Entity\User;
use App\Entity\UserLocation;
use JMS\Serializer\SerializationContext;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserLocationService
{

    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param User $user
     * @param Location $location
     *
     * @param bool $flush
     *
     * @return UserLocation
     */
    public function create(User $user, Location $location, $flush = true)
    {
        $em = $this->container->get('doctrine')->getManager();

        $entity = $this->findOneByFilter([
            'user' => $user->getId(),
            'postalCode' => $location->getPostalCode(),
            'city' => trim($location->getCity()),
            'address' => trim($location->getAddress()),
        ]);

        if ($entity) {
            return $entity;
        }

        if ($location->getId()) {
            $entity = $this->findOneByFilter([
                'user' => $user->getId(),
                'location' => $location->getId(),
            ]);

            if ($entity) {
                return $entity;
            }
        }

        $entity = new UserLocation();
        $entity->setUser($user);
        $entity->setLocation($location);

        $em->persist($entity);

        $flush && $em->flush();

        return $entity;
    }

    /**
     * @param UserLocation $location
     */
    public function remove(UserLocation $location)
    {
        $em = $this->container->get('doctrine')->getManager();

        $em->remove($location);
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

        return $em->getRepository(UserLocation::class)->countByFilter($filter);
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

        return $em->getRepository(UserLocation::class)->findByFilter($filter, $page, $limit);
    }

    /**
     * @param array $filter
     *
     * @return null|UserLocation
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
                ->setGroups(['api_v1'])), true);
    }


}
