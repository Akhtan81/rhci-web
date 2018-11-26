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

        $qb->select('partner.id')->distinct(true)
            ->addSelect('partner.createdAt');

        $qb->orderBy('partner.createdAt', 'DESC');

        if ($page > 0 && $limit > 0) {
            $qb->setMaxResults($limit)
                ->setFirstResult($limit * ($page - 1));
        }

        $items = $qb->getQuery()
            ->useQueryCache(true)
            ->getArrayResult();

        if (count($items) === 0) return [];

        $ids = array_map(function ($item) {
            return $item['id'];
        }, $items);

        $qb = $this->createFilterQuery([
            'ids' => $ids
        ]);

        $qb->orderBy('partner.createdAt', 'DESC');

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
            ->addSelect('location')
            ->addSelect('country')
            ->addSelect('user')
            ->addSelect('avatar')
            ->addSelect('code')
            ->addSelect('requestCode');

        $qb
            ->join('partner.location', 'location')
            ->join('partner.user', 'user')
            ->join('partner.country', 'country')
            ->leftJoin('user.avatar', 'avatar')
            ->leftJoin('partner.postalCodes', 'code')
            ->leftJoin('partner.requests', 'requestCode');

        foreach ($filter as $key => $value) {
            switch ($key) {
                case 'id':
                    $qb->andWhere($e->eq('partner.id', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'ids':
                    $qb->andWhere($e->in('partner.id', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'postalCode':
                    $qb->andWhere($e->eq('code.postalCode', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'type':
                    $qb->andWhere($e->eq('code.type', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'types':
                    $values = explode(',', $value);
                    if ($values) {
                        $qb->andWhere($e->in('code.type', ":$key"))
                            ->setParameter($key, $values);
                    }
                    break;
                case 'status':
                    $qb->andWhere($e->eq('partner.status', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'statuses':
                    $values = explode(',', $value);
                    if ($values) {
                        $qb->andWhere($e->in('partner.status', ":$key"))
                            ->setParameter($key, $values);
                    }
                    break;
                case 'search':
                    $qb->andWhere($e->orX()
                        ->add($e->like($e->lower('user.name'), ":$key"))
                        ->add($e->like($e->lower('user.email'), ":$key"))
                        ->add($e->like($e->lower('user.phone'), ":$key"))
                        ->add($e->like($e->lower('code.postalCode'), ":$key"))
                        ->add($e->like($e->lower('country.name'), ":$key"))
                        ->add($e->like($e->lower('location.address'), ":$key"))
                        ->add($e->like($e->lower('location.postalCode'), ":$key"))
                    )->setParameter($key, '%' . mb_strtolower($value, 'utf8') . '%');
                    break;
                case 'user':
                    $qb->andWhere($e->eq('user.id', ":$key"))
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
                case 'canManagerOrders':
                    if ($value) {
                        $qb->andWhere($e->orX()
                            ->add($e->isNotNull('partner.accountId'))
                            ->add($e->isNotNull('partner.cardToken'))
                        );
                    } else {
                        $qb->andWhere($e->orX()
                            ->add($e->isNull('partner.accountId'))
                            ->add($e->isNull('partner.cardToken'))
                        );
                    }
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