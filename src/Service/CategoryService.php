<?php

namespace App\Service;

use App\Entity\Category;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\SerializationContext;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CategoryService
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
     * @return Category
     * @throws \Exception
     */
    public function create($content)
    {
        $entity = new Category();

        $this->update($entity, $content);

        return $entity;
    }

    /**
     * @param Category $entity
     * @param $content
     *
     * @throws \Exception
     */
    public function update(Category $entity, $content)
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
     * @param Category $entity
     *
     * @throws \Exception
     */
    public function remove(Category $entity)
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

        return $em->getRepository(Category::class)->countByFilter($filter);
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

        return $em->getRepository(Category::class)->findByFilter($filter, $page, $limit);
    }

    /**
     * @param array $filter
     *
     * @return Category|null
     */
    public function findOneByFilter(array $filter = [])
    {
        $items = $this->findByFilter($filter, 1, 1);
        if (count($items) !== 1) return null;

        return $items[0];
    }

    /**
     * @param array $entities
     *
     * @return array
     */
    public function buildTree(array $entities)
    {
        $levelRegistry = [];
        $minLevel = 0;
        $maxLevel = 0;

        /** @var Category $entity */
        foreach ($entities as $entity) {

            $lvl = $entity->getLvl();
            $id = $entity->getId();

            if (!isset($levelRegistry[$lvl])) {
                $levelRegistry[$lvl] = [];
            }

            $levelRegistry[$lvl][$id] = $entity;

            if ($lvl > $maxLevel) $maxLevel = $lvl;
        }

        for ($level = $maxLevel; $level > 0; $level--) {
            $currentLevelItems = $levelRegistry[$level];
            $parentLevelItems = $levelRegistry[$level - 1];

            /** @var Category $currentItem */
            foreach ($currentLevelItems as $currentItem) {
                $parentId = $currentItem->getParent()->getId();

                /** @var Category $parentCategory */
                $parentCategory = $parentLevelItems[$parentId];

                $parentCategory->addChild($currentItem);
            }
        }

        if (!isset($levelRegistry[$minLevel])) return [];

        return array_values($levelRegistry[$minLevel]);
    }

    public function serialize($content)
    {
        return json_decode($this->container->get('jms_serializer')
            ->serialize($content, 'json', SerializationContext::create()
                ->setGroups(['api_v1'])), true);
    }


}