<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class UserLocationRepository extends EntityRepository
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

        $qb->select('userLocation.id')->distinct(true)
            ->addSelect('userLocation.createdAt');

        $qb->orderBy('userLocation.createdAt', 'DESC');

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

        $qb->orderBy('userLocation.createdAt', 'DESC');

        return $qb->getQuery()
            ->useQueryCache(true)
            ->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true)
            ->getResult();
    }

    private function createFilterQuery($filter = [])
    {
        $qb = $this->createQueryBuilder('userLocation');
        $e = $qb->expr();

        $qb
            ->addSelect('user')
            ->addSelect('location')
            ->addSelect('country')
            ->addSelect('countryTranslation');

        $qb
            ->join('userLocation.user', 'user')
            ->join('userLocation.location', 'location')
            ->leftJoin('location.country', 'country')
            ->leftJoin('country.translations', 'countryTranslation');

        foreach ($filter as $key => $value) {
            if (!$value) continue;

            switch ($key) {
                case 'id':
                    $qb->andWhere($e->eq('userLocation.id', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'ids':
                    $qb->andWhere($e->in('userLocation.id', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'user':
                    $qb->andWhere($e->eq('user.id', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'country':
                    $qb->andWhere($e->eq('country.id', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'location':
                    $qb->andWhere($e->eq('location.id', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'postalCode':
                    $qb->andWhere($e->eq('location.postalCode', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'address':
                    $qb->andWhere($e->eq($e->lower('location.address'), ":$key"))
                        ->setParameter($key, mb_strtolower($value, 'utf8'));
                    break;
                case 'city':
                    $qb->andWhere($e->eq($e->lower('location.city'), ":$key"))
                        ->setParameter($key, mb_strtolower($value, 'utf8'));
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

        $qb->select($e->countDistinct('userLocation.id'));

        return $qb->getQuery()
            ->useQueryCache(true)
            ->getSingleScalarResult();
    }
}
