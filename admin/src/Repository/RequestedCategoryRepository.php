<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class RequestedCategoryRepository extends EntityRepository
{

    /**
     * @param array $filter
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function findIdsByFilter($filter = [], $page = 0, $limit = 0)
    {
        $qb = $this->createFilterQuery($filter);

        $qb->select('requestedCategory.id')->distinct(true)
            ->addSelect('requestedCategory.createdAt');

        $qb->orderBy('requestedCategory.createdAt', 'DESC');

        if ($page > 0 && $limit > 0) {
            $qb->setMaxResults($limit)
                ->setFirstResult($limit * ($page - 1));
        }

        $items = $qb->getQuery()
            ->useQueryCache(true)
            ->getArrayResult();

        return array_map(function ($item) {
            return $item['id'];
        }, $items);
    }

    /**
     * @param array $filter
     * @param int $page
     * @param int $limit
     *
     * @return array
     */
    public function findByFilter($filter = [], $page = 0, $limit = 0)
    {
        $ids = $this->findIdsByFilter($filter, $page, $limit);

        if (count($ids) === 0) return [];

        $qb = $this->createFilterQuery([
            'ids' => $ids
        ]);

        $qb->orderBy('requestedCategory.createdAt', 'DESC');

        return $qb->getQuery()
            ->useQueryCache(true)
            ->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true)
            ->getResult();
    }

    private function createFilterQuery($filter = [])
    {
        $qb = $this->createQueryBuilder('requestedCategory');
        $e = $qb->expr();

        $qb
            ->addSelect('partner')
            ->addSelect('category')
            ->addSelect('translation');

        $qb
            ->join('requestedCategory.partner', 'partner')
            ->join('requestedCategory.category', 'category')
            ->join('category.translations', 'translation');

        foreach ($filter as $key => $value) {
            switch ($key) {
                case 'id':
                    $qb->andWhere($e->eq('requestedCategory.id', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'ids':
                    $qb->andWhere($e->in('requestedCategory.id', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'partner':
                    $qb->andWhere($e->eq('partner.id', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'partners':
                    $qb->andWhere($e->in('partner.id', ":$key"))
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

        $qb->select($e->countDistinct('requestedCategory.id'));

        return $qb->getQuery()
            ->useQueryCache(true)
            ->getSingleScalarResult();
    }
}