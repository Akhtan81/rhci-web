<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\CategoryTranslation;
use App\Entity\CategoryType;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\PartnerCategory;
use App\Entity\PartnerPostalCode;
use App\Entity\RequestedCategory;
use App\Entity\RequestedCategoryStatus;
use App\Entity\Unit;
use App\Entity\UnitTranslation;
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
        $em = $this->container->get('doctrine')->getManager();
        $trans = $this->container->get('translator');
        $localeService = $this->container->get(LocaleService::class);

        $entity = new Category();

        foreach ($localeService->getSupportedLocales() as $supportedLocale) {

            $trans = new CategoryTranslation();
            $trans->setLocale($supportedLocale);
            $trans->setCategory($entity);
            $trans->setName('');

            $em->persist($trans);

            $entity->addTranslation($trans);
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

        if (isset($content['translations'])) {
            foreach ($content['translations'] as $translationContent) {

                /** @var CategoryTranslation $translation */
                foreach ($entity->getTranslations() as $translation) {

                    if ($translation->getLocale() === $translationContent['locale']) {

                        if (isset($translationContent['name'])) {
                            $translation->setName(trim($translationContent['name']));
                        }
                    }

                    $em->persist($translation);
                }
            }
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
        $soft->disableForEntity(UnitTranslation::class);
        $soft->disableForEntity(Category::class);
        $soft->disableForEntity(CategoryTranslation::class);

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

        $translations = $em->getRepository(CategoryTranslation::class)->findBy([
            'category' => $entity->getId()
        ]);

        /** @var CategoryTranslation $translation */
        foreach ($translations as $translation) {

            $translation->setDeletedAt($now);

            $em->persist($translation);
        }

        $entity->setDeletedAt($now);

        $em->persist($entity);
        $em->flush();

        $soft->enableForEntity(PartnerCategory::class);
        $soft->enableForEntity(PartnerPostalCode::class);
        $soft->enableForEntity(Order::class);
        $soft->enableForEntity(Unit::class);
        $soft->enableForEntity(UnitTranslation::class);
        $soft->enableForEntity(Category::class);
        $soft->enableForEntity(CategoryTranslation::class);
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

    public function serialize($content, $locale, $groups = [])
    {
        $groups[] = 'api_v1';

        $result = json_decode($this->container->get('jms_serializer')
            ->serialize($content, 'json', SerializationContext::create()
                ->setGroups($groups)), true);

        if ($content instanceof Category) {
            $this->onPostSerialize($result, $locale);
        } else {
            foreach ($result as &$item) {
                $this->onPostSerialize($item, $locale);
            }
        }
        return $result;
    }

    public function serializeV2($content, $locale)
    {
        return $this->serialize($content, $locale, ['api_v2']);
    }

    public function onPostSerialize(&$content, $locale)
    {
        $isAdmin = $this->container->get(UserService::class)->getAdmin();

        if (isset($content['translations']) && count($content['translations'])) {

            $translation = null;

            foreach ($content['translations'] as $item) {
                if ($item['locale'] === $locale) {
                    $translation = $item;
                    break;
                }
            }

            if (!$translation) {
                $translation = $content['translations'][0];
            }

            $content['name'] = $translation['name'];
            $content['locale'] = $translation['locale'];

            if (!$isAdmin) {
                unset($content['translations']);
            }
        }

        if (isset($content['children'])) {
            foreach ($content['children'] as &$child) {
                $this->onPostSerialize($child, $locale);
            }

        }
    }

}