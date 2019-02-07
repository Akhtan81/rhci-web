<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class PartnerCategoryRepository extends EntityRepository
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

        $qb->select('partnerCategory.id')->distinct(true);

        $qb->orderBy('partnerCategory.id', 'DESC');

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

        $qb->orderBy('partnerCategory.id', 'DESC');

        return $qb->getQuery()
            ->useQueryCache(true)
            ->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true)
            ->getResult();
    }

    private function createFilterQuery($filter = [])
    {
        $qb = $this->createQueryBuilder('partnerCategory');
        $e = $qb->expr();

        $qb
            ->addSelect('unit')
            ->addSelect('unitTranslation')
            ->addSelect('user')
            ->addSelect('avatar')
            ->addSelect('partner')
            ->addSelect('category')
            ->addSelect('categoryTranslation')
            ->addSelect('parent');

        $qb
            ->join('partnerCategory.partner', 'partner')
            ->join('partnerCategory.category', 'category')
            ->join('category.translations', 'categoryTranslation')
            ->leftJoin('category.parent', 'parent')
            ->leftJoin('partnerCategory.unit', 'unit')
            ->leftJoin('unit.translations', 'unitTranslation')
            ->join('partner.user', 'user')
            ->leftJoin('user.avatar', 'avatar');

        foreach ($filter as $key => $value) {
            if (!$value) continue;

            switch ($key) {
                case 'id':
                    $qb->andWhere($e->eq('partnerCategory.id', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'ids':
                    $qb->andWhere($e->in('partnerCategory.id', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'partner':
                    $qb->andWhere($e->eq('partner.id', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'partnerStatus':
                    $qb->andWhere($e->eq('partner.status', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'type':
                    $qb->andWhere($e->eq('category.type', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'category':
                    $qb->andWhere($e->eq('category.id', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'minAmount':
                    $qb->andWhere($e->eq('partnerCategory.minAmount', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'unit':
                    $qb->andWhere($e->eq('unit.id', ":$key"))
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

        $qb->select($e->countDistinct('partnerCategory.id'));

        return $qb->getQuery()
            ->useQueryCache(true)
            ->getSingleScalarResult();
    }
}