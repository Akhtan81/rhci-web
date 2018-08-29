<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class OrderRepository extends EntityRepository
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

        $qb->orderBy('entity.createdAt', 'DESC');

        $qb->select('entity.id')->distinct(true)
            ->addSelect('entity.createdAt');

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

        $qb->orderBy('entity.createdAt', 'DESC');

        return $qb->getQuery()
            ->useQueryCache(true)
            ->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true)
            ->getResult();
    }

    private function createFilterQuery($filter = [])
    {
        $qb = $this->createQueryBuilder('entity');
        $e = $qb->expr();

        $qb
            ->addSelect('user')
            ->addSelect('partner')
            ->addSelect('district')
            ->addSelect('updatedBy')
            ->addSelect('message')
            ->addSelect('messageUser')
            ->addSelect('messageMedia')
            ->addSelect('media')
            ->addSelect('item')
            ->addSelect('category')
            ->addSelect('partnerCategory');

        $qb
            ->join('entity.user', 'user')
            ->join('entity.messages', 'message')
            ->join('message.user', 'messageUser')
            ->leftJoin('message.media', 'messageMedia')
            ->leftJoin('messageMedia.media', 'media')
            ->join('entity.items', 'item')
            ->join('item.category', 'category')
            ->leftJoin('item.partnerCategory', 'partnerCategory')
            ->leftJoin('entity.partner', 'partner')
            ->leftJoin('entity.district', 'district')
            ->leftJoin('entity.updatedBy', 'updatedBy');

        foreach ($filter as $key => $value) {
            if (!$value) continue;

            switch ($key) {
                case 'id':
                    $qb->andWhere($e->eq('entity.id', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'ids':
                    $qb->andWhere($e->in('entity.id', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'user':
                    $qb->andWhere($e->eq('user.id', ":$key"))
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

        $qb->select($e->countDistinct('entity.id'));

        return $qb->getQuery()
            ->useQueryCache(true)
            ->getSingleScalarResult();
    }
}