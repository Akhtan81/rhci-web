<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class CreditCardRepository extends EntityRepository
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

        $qb->orderBy('card.createdAt', 'DESC');

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
        $qb = $this->createQueryBuilder('card');
        $e = $qb->expr();

        $qb
            ->addSelect('user')
            ->addSelect('primaryCreditCard');

        $qb
            ->join('card.user', 'user')
            ->leftJoin('user.primaryCreditCard', 'primaryCreditCard');

        foreach ($filter as $key => $value) {
            if (!$value) continue;

            switch ($key) {
                case 'id':
                    $qb->andWhere($e->eq('card.id', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'isPrimary':
                    $qb->andWhere($e->eq('primaryCreditCard.id', "card.id"));
                    break;
                case 'user':
                    $qb->andWhere($e->eq('user.id', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'token':
                    $qb->andWhere($e->eq('card.token', ":$key"))
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

        $qb->select($e->countDistinct('card.id'));

        return $qb->getQuery()
            ->useQueryCache(true)
            ->getSingleScalarResult();
    }
}