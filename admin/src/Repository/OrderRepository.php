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


    /**
     * @param array $filter
     * @param int $page
     * @param int $limit
     *
     * @return array
     */
    public function findLocationsByFilter($filter = [], $page = 0, $limit = 0)
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

        $qb
            ->select('entity')
            ->addSelect('user')
            ->addSelect('orderLocation');

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
            ->addSelect('payment')
            ->addSelect('user')
            ->addSelect('partner')
            ->addSelect('partnerUser')
            ->addSelect('partnerUserAvatar')
            ->addSelect('partnerCountry')
            ->addSelect('updatedBy')
            ->addSelect('updatedByAvatar')
            ->addSelect('message')
            ->addSelect('messageUser')
            ->addSelect('messageUserAvatar')
            ->addSelect('messageMedia')
            ->addSelect('media')
            ->addSelect('item')
            ->addSelect('category')
            ->addSelect('partnerCategory')
            ->addSelect('unit')
            ->addSelect('orderLocation')
            ->addSelect('itemMessage')
            ->addSelect('itemMessageUser')
            ->addSelect('itemMessageUserAvatar')
            ->addSelect('itemMessageMedia')
            ->addSelect('itemMedia');

        $qb
            ->leftJoin('entity.payments', 'payment')
            ->join('entity.location', 'orderLocation')
            ->join('entity.user', 'user')
            ->leftJoin('entity.messages', 'message')
            ->leftJoin('message.user', 'messageUser')
            ->leftJoin('messageUser.avatar', 'messageUserAvatar')
            ->leftJoin('message.media', 'messageMedia')
            ->leftJoin('messageMedia.media', 'media')
            ->join('entity.items', 'item')
            ->join('item.category', 'category')
            ->join('item.partnerCategory', 'partnerCategory')
            ->leftJoin('partnerCategory.unit', 'unit')
            ->leftJoin('entity.partner', 'partner')
            ->leftJoin('partner.user', 'partnerUser')
            ->leftJoin('partner.country', 'partnerCountry')
            ->leftJoin('partnerUser.avatar', 'partnerUserAvatar')
            ->join('entity.updatedBy', 'updatedBy')
            ->leftJoin('updatedBy.avatar', 'updatedByAvatar')
            ->leftJoin('item.message', 'itemMessage')
            ->leftJoin('itemMessage.user', 'itemMessageUser')
            ->leftJoin('itemMessageUser.avatar', 'itemMessageUserAvatar')
            ->leftJoin('itemMessage.media', 'itemMessageMedia')
            ->leftJoin('itemMessageMedia.media', 'itemMedia');

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
                case 'type':
                    $qb->andWhere($e->eq('entity.type', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'status':
                    $qb->andWhere($e->eq('entity.status', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'statuses':
                    $qb->andWhere($e->in('entity.status', ":$key"))
                        ->setParameter($key, explode(',', $value));
                    break;
                case 'category':
                    $qb->andWhere($e->eq('category.id', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'partnerCategory':
                    $qb->andWhere($e->eq('partnerCategory.id', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'partner':
                    $qb->andWhere($e->eq('partner.id', ":$key"))
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