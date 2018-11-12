<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class PartnerSubscriptionRepository extends EntityRepository
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

        $qb->orderBy('subscription.createdAt', 'DESC');

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
        $qb = $this->createQueryBuilder('subscription');
        $e = $qb->expr();

        $qb
            ->addSelect('partner')
            ->addSelect('user');

        $qb
            ->join('subscription.partner', 'partner')
            ->join('partner.user', 'user');

        foreach ($filter as $key => $value) {
            if (!$value) continue;

            switch ($key) {
                case 'id':
                    $qb->andWhere($e->eq('subscription.id', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'providerId':
                    $qb->andWhere($e->eq('subscription.providerId', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'status':
                    $qb->andWhere($e->eq('subscription.status', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'partner':
                    $qb->andWhere($e->eq('partner.id', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'customerId':
                    $qb->andWhere($e->eq('partner.customerId', ":$key"))
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

        $qb->select($e->countDistinct('subscription.id'));

        return $qb->getQuery()
            ->useQueryCache(true)
            ->getSingleScalarResult();
    }
}