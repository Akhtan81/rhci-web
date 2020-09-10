<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class GroupRepository extends EntityRepository
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

        $qb->select('group.id')->distinct(true)
            ->addSelect('group.name_en')
            ->addSelect('group.bidirectional');

        $qb->orderBy('group.id', 'ASC')
            ->addOrderBy('group.name_en', 'ASC');

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

        $qb->orderBy('group.id', 'ASC')
            //->addOrderBy('group.ordering', 'ASC')
            ->addOrderBy('group.id', 'DESC');

        $items = $qb->getQuery()
            ->useQueryCache(true)
            ->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true)
            ->getResult();

        return $items;
    }

    private function createFilterQuery($filter = [])
    {
        $qb = $this->createQueryBuilder('group');
        $e = $qb->expr();

        $qb
            ->addSelect('name_en')
            ->addSelect('bidirectional');

        /*$qb
            ->join('group.translations', 'translation')
            ->leftJoin('group.parent', 'parent');*/

        foreach ($filter as $key => $value) {

            switch ($key) {
                case 'id':
                    $qb->andWhere($e->eq('group.id', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'ids':
                    $qb->andWhere($e->in('group.id', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'name':
                    $qb->andWhere($e->eq('group.name_en', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'bidirectional':
                    $qb->andWhere($e->eq('group.bidirectional', ":$key"))
                        ->setParameter($key, $value);
                    break;
                /*case 'parent':
                    $qb->andWhere($e->eq('parent.id', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'lvl':
                    $qb->andWhere($e->eq('group.lvl', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'lvl|lt':
                    $qb->andWhere($e->lt('group.lvl', ":lvl_lt"))
                        ->setParameter('lvl_lt', $value);
                    break;*/
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

        $qb->select($e->countDistinct('group.id'));

        return $qb->getQuery()
            ->useQueryCache(true)
            ->getSingleScalarResult();
    }
}