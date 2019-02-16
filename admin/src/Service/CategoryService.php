<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\CategoryType;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\PartnerCategory;
use App\Entity\PartnerPostalCode;
use App\Entity\RequestedCategory;
use App\Entity\RequestedCategoryStatus;
use App\Entity\Unit;
use Gedmo\SoftDeleteable\Filter\SoftDeleteableFilter;
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
        $trans = $this->container->get('translator');
        $defaultLocale = $this->container->getParameter('locale');
        $locales = explode('|', $this->container->getParameter('supported_locales'));

        $entity = new Category();

        if (isset($content['locale'])) {
            if (!in_array($content['locale'], $locales)) {
                throw new \Exception($trans->trans('validation.invalid_locale'), 400);
            }
            $entity->setLocale($content['locale']);
        } else {
            $entity->setLocale($defaultLocale);
        }

        if (isset($content['type'])) {
            switch ($content['type']) {
                case CategoryType::JUNK_REMOVAL:
                case CategoryType::RECYCLING:
                case CategoryType::SHREDDING:
                case CategoryType::DONATION:
                    $entity->setType($content['type']);
                    break;
                default:
                    throw new \Exception($trans->trans('validation.invalid_category_type'), 400);
            }
        }

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

        if (isset($content['ordering'])) {
            $entity->setOrdering(intval($content['ordering']));
        }

        if (isset($content['name'])) {
            $entity->setName(trim($content['name']));
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
        $orderService = $this->container->get(OrderService::class);
        $partnerCategoryService = $this->container->get(PartnerCategoryService::class);

        /** @var SoftDeleteableFilter $soft */
        $soft = $em->getFilters()->getFilter('softdeleteable');

        $soft->disableForEntity(PartnerCategory::class);
        $soft->disableForEntity(PartnerPostalCode::class);
        $soft->disableForEntity(Order::class);
        $soft->disableForEntity(Unit::class);

        $childrenCount = $this->countByFilter([
            'parent' => $entity->getId()
        ]);
        if ($childrenCount > 0) {
            throw new \Exception($trans->trans('validation.category_has_child'), 400);
        }

        $orderCount = $orderService->countByFilter([
            'category' => $entity->getId()
        ]);
        if ($orderCount > 0) {
            throw new \Exception($trans->trans('validation.category_has_orders'), 400);
        }

        $orderItem = $em->getRepository(OrderItem::class)->findOneBy([
            'category' => $entity->getId()
        ]);
        if ($orderItem) {
            throw new \Exception($trans->trans('validation.category_has_orders'), 400);
        }

        $now = new \DateTime();

        $partnerCategories = $partnerCategoryService->findByFilter([
            'category' => $entity->getId()
        ]);
        /** @var PartnerCategory $partnerCategory */
        foreach ($partnerCategories as $partnerCategory) {

            $partnerCategory->setDeletedAt($now);

            $em->persist($partnerCategory);
        }

        $entity->setDeletedAt($now);

        $em->persist($entity);
        $em->flush();

        $soft->enableForEntity(PartnerCategory::class);
        $soft->enableForEntity(PartnerPostalCode::class);
        $soft->enableForEntity(Order::class);
        $soft->enableForEntity(Unit::class);
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
        $userService = $this->container->get(UserService::class);
        $em = $this->container->get('doctrine')->getManager();
        $partner = $userService->getPartner();

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
            $currentLevelItems = $levelRegistry[$level] ?? [];
            $parentLevelItems = $levelRegistry[$level - 1] ?? [];

            /** @var Category $currentItem */
            foreach ($currentLevelItems as $currentItem) {
                $parentId = $currentItem->getParent()->getId();

                if (isset($parentLevelItems[$parentId])) {
                    /** @var Category $parentCategory */
                    $parentCategory = $parentLevelItems[$parentId];

                    $parentCategory->addChild($currentItem);
                } else {
                    $levelRegistry[0][] = $currentItem;
                }
            }
        }

        if (!isset($levelRegistry[$minLevel])) return [];

        $topCategories = array_values($levelRegistry[$minLevel]);

        if (!$partner) {
            return $topCategories;
        }

        $filtered = [];

        /** @var Category $root */
        $root = $topCategories[0];

        switch ($root->getType()) {
            case CategoryType::RECYCLING:
                $approvedCategories = $em->getRepository(RequestedCategory::class)->findBy([
                    'status' => RequestedCategoryStatus::APPROVED,
                    'partner' => $partner->getId()
                ]);

                $ids = [];

                /** @var RequestedCategory $approvedCategory */
                foreach ($approvedCategories as $approvedCategory) {
                    $ids[] = $approvedCategory->getCategory()->getId();
                }

                /** @var Category $topCategory */
                foreach ($topCategories as $key => $topCategory) {
                    if (in_array($topCategory->getId(), $ids)) {
                        $filtered[] = $topCategory;
                    }
                }
                break;
            default:
                $filtered = $topCategories;
        }

        return $filtered;
    }

    public function serialize($content, $groups = [])
    {
        $groups[] = 'api_v1';

        return json_decode($this->container->get('jms_serializer')
            ->serialize($content, 'json', SerializationContext::create()
                ->setGroups($groups)), true);
    }

    public function serializeV2($content)
    {
        return $this->serialize($content, ['api_v2']);
    }


}