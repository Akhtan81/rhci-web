<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\CategoryType;
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

        $partnerCategoryService = $this->container->get(PartnerCategoryService::class);
        $partnerService = $this->container->get(PartnerService::class);
        $trans = $this->container->get('translator');
        $locales = explode('|', $this->container->getParameter('supported_locales'));

        $entity = new Category();

        if (isset($content['locale'])) {
            if (!in_array($content['locale'], $locales)) {
                throw new \Exception($trans->trans('validation.invalid_locale'), 400);
            }
            $entity->setLocale($content['locale']);
        }

        if (isset($content['type'])) {
            switch ($content['type']) {
                case CategoryType::JUNK_REMOVAL:
                case CategoryType::RECYCLING:
                case CategoryType::SHREDDING:
                    $entity->setType($content['type']);
                    break;
                default:
                    throw new \Exception($trans->trans('validation.invalid_category_type'), 400);
            }
        }

        $this->update($entity, $content);

        $partners = $partnerService->findByFilter();
        foreach ($partners as $partner) {
            $partnerCategoryService->create($partner, $entity);
        }

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

        if (isset($content['name'])) {
            $entity->setName(trim($content['name']));
        }

        if (isset($content['price'])) {
            $entity->setPrice($content['price']);
        }

        if (isset($content['isSelectable'])) {
            $entity->setSelectable($content['isSelectable'] === true);
        }

        if (isset($content['hasPrice'])) {
            $entity->setHasPrice($content['hasPrice'] === true);
        }

        if (isset($content['parent']) && $content['parent'] !== $entity->getId()) {
            $parent = $this->findOneByFilter([
                'id' => $content['parent']
            ]);
            if (!$parent) {
                throw new \Exception($trans->trans('validation.category_was_not_found'), 404);
            }

            $entity->setParent($parent);
            $entity->setLvl($parent->getLvl() + 1);
        } else {
            $entity->setParent(null);
            $entity->setLvl(0);
        }

        $match = $this->findOneByFilter([
            'type' => $entity->getType(),
            'locale' => $entity->getLocale(),
            'name' => $entity->getName(),
            'lvl' => $entity->getLvl()
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

        $childrenCount = $this->countByFilter([
            'parent' => $entity->getId()
        ]);
        if ($childrenCount > 0) {
            throw new \Exception($trans->trans('validation.category_has_child'), 400);
        }

        $partnerCategoryService = $this->container->get(PartnerCategoryService::class);

        $partnerCategories = $partnerCategoryService->findByFilter([
            'category' => $entity->getId()
        ]);
        foreach ($partnerCategories as $partnerCategory) {
            $em->remove($partnerCategory);
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

    public function serializeV2($content)
    {
        return json_decode($this->container->get('jms_serializer')
            ->serialize($content, 'json', SerializationContext::create()
                ->setGroups(['api_v1', 'api_v2'])), true);
    }


}