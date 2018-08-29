<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class PaymentRepository extends EntityRepository
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

        $qb->orderBy('payment.createdAt', 'DESC');

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
        $qb = $this->createQueryBuilder('payment');
        $e = $qb->expr();

        $qb->addSelect('order');

        $qb->join('payment.order', 'order');

        foreach ($filter as $key => $value) {
            if (!$value) continue;

            switch ($key) {
                case 'status':
                    $qb->andWhere($e->eq('payment.status', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'type':
                    $qb->andWhere($e->eq('payment.type', ":$key"))
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

        $qb->select($e->countDistinct('payment.id'));

        return $qb->getQuery()
            ->useQueryCache(true)
            ->getSingleScalarResult();
    }
}