<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class PartnerCategoryRepository extends EntityRepository
{

    /**
     * @param array $filter
     * @param int $page
     * @param int $limit
     *
     * @return array
     */
    public function findByFilter($filter = [], $page = 0, $limit = 0)
    {
        $qb = $this->createFilterQuery($filter);

        $qb
            ->orderBy('category.lvl', 'ASC')
            ->addOrderBy('category.ordering', 'ASC')
            ->addOrderBy('category.name', 'ASC');

        if ($page > 0 && $limit > 0) {
            $qb->setMaxResults($limit)
                ->setFirstResult($limit * ($page - 1));
        }

        return $qb->getQuery()
            ->useQueryCache(true)
            ->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true)
            ->getResult();
    }

    private function createFilterQuery($filter = [])
    {
        $qb = $this->createQueryBuilder('partnerCategory');
        $e = $qb->expr();

        $qb
            ->addSelect('partner')
            ->addSelect('category')
            ->addSelect('parent');

        $qb
            ->join('partnerCategory.partner', 'partner')
            ->join('partnerCategory.category', 'category')
            ->leftJoin('category.parent', 'parent');

        foreach ($filter as $key => $value) {
            if (!$value) continue;

            switch ($key) {
                case 'id':
                    $qb->andWhere($e->eq('partnerCategory.id', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'partner':
                    $qb->andWhere($e->eq('partner.id', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'partnerStatus':
                    $qb->andWhere($e->eq('partner.status', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'type':
                    $qb->andWhere($e->eq('category.type', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'locale':
                    $qb->andWhere($e->eq('category.locale', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'category':
                    $qb->andWhere($e->eq('category.id', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'isSelectable':
                    $qb->andWhere($e->eq('category.isSelectable', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'hasPrice':
                    $qb->andWhere($e->eq('category.hasPrice', ":$key"))
                        ->setParameter($key, $value);
                    break;
            }
        }

        return $qb;
    }

    /**
     * @param array $filter
     *
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countByFilter($filter = [])
    {
        $qb = $this->createFilterQuery($filter);
        $e = $qb->expr();

        $qb->select($e->countDistinct('partnerCategory.id'));

        return $qb->getQuery()
            ->useQueryCache(true)
            ->getSingleScalarResult();
    }
}