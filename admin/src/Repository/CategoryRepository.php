<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class CategoryRepository extends EntityRepository
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

        $qb->select('category.id')->distinct(true);

        $qb->orderBy('category.id', 'DESC');

        if ($page > 0 && $limit > 0) {
            $qb->setMaxResults($limit)
                ->setFirstResult($limit * ($page - 1));
        }

        $result = $qb->getQuery()
            ->useQueryCache(true)
            ->getArrayResult();

        if (count($result) === 0) return [];

        $ids = array_map(function ($item) {
            return $item['id'];
        }, $result);

        $qb = $this->createFilterQuery([
            'ids' => $ids
        ]);

        $qb->orderBy('category.id', 'DESC');

        $items = $qb->getQuery()
            ->useQueryCache(true)
            ->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true)
            ->getResult();

        return $items;
    }

    private function createFilterQuery($filter = [])
    {
        $qb = $this->createQueryBuilder('category');
        $e = $qb->expr();

        $qb
            ->addSelect('parent')
            ->addSelect('translation');

        $qb
            ->join('category.translations', 'translation')
            ->leftJoin('category.parent', 'parent');

        foreach ($filter as $key => $value) {

            switch ($key) {
                case 'id':
                    $qb->andWhere($e->eq('category.id', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'ids':
                    $qb->andWhere($e->in('category.id', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'name':
                    $qb->andWhere($e->eq('translation.name', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'type':
                    $qb->andWhere($e->eq('category.type', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'parent':
                    $qb->andWhere($e->eq('parent.id', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'lvl':
                    $qb->andWhere($e->eq('category.lvl', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'lvl|lt':
                    $qb->andWhere($e->lt('category.lvl', ":lvl_lt"))
                        ->setParameter('lvl_lt', $value);
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

        $qb->select($e->countDistinct('category.id'));

        return $qb->getQuery()
            ->useQueryCache(true)
            ->getSingleScalarResult();
    }
}