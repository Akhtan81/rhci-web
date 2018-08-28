<?php

namespace App\Service;

use App\Entity\Category;
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

        $match = $em->getRepository(Category::class)->findOneBy([
            'name' => $entity->getName()
        ]);
        if ($match && $match !== $entity) {
            throw new \Exception($trans->trans('validation.non_unique_category'), 400);
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
        $courseService = $this->container->get(CourseService::class);

        $count = $courseService->countByFilter([
            'category' => $entity->getId()
        ]);
        if ($count > 0) {
            throw new \Exception($trans->trans('validation.category_not_removed_has_courses'), 400);
        }

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

    public function serialize($content)
    {
        return json_decode($this->container->get('jms_serializer')
            ->serialize($content, 'json', SerializationContext::create()
                ->setGroups(['api_v1'])), true);
    }


}