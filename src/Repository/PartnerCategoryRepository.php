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
     * @return array
     */
    public function findByFilter($filter = [], $page = 0, $limit = 0)
    {
        $qb = $this->createFilterQuery($filter);

        $qb->orderBy('partnerCategory.createdAt', 'DESC');

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
            ->addSelect('region')
            ->addSelect('category');

        $qb
            ->join('partnerCategory.partner', 'partner')
            ->join('partnerCategory.category', 'category')
            ->join('partner.region', 'region')
        ;

        foreach ($filter as $key => $value) {
            if (!$value) continue;

            switch ($key) {
                case 'id':
                    $qb->andWhere($e->eq('partnerCategory.id', ":$key"))
                        ->setParameter($key, $value);
                    break;
            }
        }

        return $qb;
    }

    /**
     * @param array $filter
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