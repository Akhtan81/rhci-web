<?php

namespace App\Service;

use App\Entity\Media;
use JMS\Serializer\SerializationContext;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MediaService
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
     * @return Media
     * @throws \Exception
     */
    public function create($content)
    {
        $entity = new Media();

        $this->update($entity, $content);

        return $entity;
    }

    /**
     * @param Media $entity
     * @param $content
     *
     * @throws \Exception
     */
    public function update(Media $entity, $content)
    {
        $trans = $this->container->get('translator');
        $em = $this->container->get('doctrine')->getManager();

        if (isset($content['name']) && $content['name']) {
            $entity->setName(trim($content['name']));
        }

        $em->persist($entity);
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

        return $em->getRepository(Media::class)->countByFilter($filter);
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

        return $em->getRepository(Media::class)->findByFilter($filter, $page, $limit);
    }

    /**
     * @param array $filter
     *
     * @return Media|null
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