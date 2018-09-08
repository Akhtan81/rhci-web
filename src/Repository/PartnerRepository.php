<?php

namespace App\Repository;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class PartnerRepository extends EntityRepository
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

        $qb->orderBy('partner.createdAt', 'DESC');

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
        $qb = $this->createQueryBuilder('partner');
        $e = $qb->expr();

        $qb
            ->addSelect('user')
            ->addSelect('district');

        $qb
            ->join('partner.user', 'user')
            ->join('partner.district', 'district')
            ->join('district.city', 'city')
            ->join('city.region', 'region')
            ->join('region.country', 'country');

        foreach ($filter as $key => $value) {
            switch ($key) {
                case 'search':
                    $qb->andWhere($e->orX()
                        ->add($e->like($e->lower('user.name'), ":$key"))
                        ->add($e->like($e->lower('district.postalCode'), ":$key"))
                        ->add($e->like($e->lower('district.name'), ":$key"))
                        ->add($e->like($e->lower('district.fullName'), ":$key"))
                        ->add($e->like($e->lower('city.name'), ":$key"))
                        ->add($e->like($e->lower('city.fullName'), ":$key"))
                        ->add($e->like($e->lower('region.name'), ":$key"))
                        ->add($e->like($e->lower('region.fullName'), ":$key"))
                        ->add($e->like($e->lower('country.name'), ":$key"))
                    )->setParameter($key, '%' . mb_strtolower($value, 'utf8') . '%');
                    break;
                case 'user':
                    $qb->andWhere($e->eq('user.id', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'district':
                    $qb->andWhere($e->eq('district.id', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'city':
                    $qb->andWhere($e->eq('city.id', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'region':
                    $qb->andWhere($e->eq('region.id', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'country':
                    $qb->andWhere($e->eq('country.id', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'isActive':
                    $value = intval($value) === 1;
                    $qb->andWhere($e->eq('user.isActive', ":$key"))
                        ->setParameter($key, $value, Type::BOOLEAN);
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

        $qb->select($e->countDistinct('partner.id'));

        return $qb->getQuery()
            ->useQueryCache(true)
            ->getSingleScalarResult();
    }
}