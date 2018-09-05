<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository implements UserLoaderInterface
{
    /**
     * @param string $username
     *
     * @return null|User
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function loadUserByUsername($username)
    {
        if (!$username) return null;

        $qb = $this->createFilterQuery([
            'isActive' => true,
            'login' => $username
        ]);

        return $qb->getQuery()
            ->useQueryCache(true)
            ->getOneOrNullResult();
    }

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

        $qb->orderBy('user.id', 'DESC');

        $qb->select('user.id')->distinct(true);

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

        return $qb->getQuery()
            ->useQueryCache(true)
            ->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true)
            ->getResult();
    }

    private function createFilterQuery($filter = [])
    {
        $qb = $this->createQueryBuilder('user');
        $e = $qb->expr();

        $qb
            ->addSelect('currentLocation')
            ->addSelect('prevLocation')
            ->addSelect('avatar')
            ->addSelect('partner')
            ->addSelect('district');

        $qb
            ->leftJoin('user.location', 'currentLocation')
            ->leftJoin('user.locations', 'prevLocation')
            ->leftJoin('user.avatar', 'avatar')
            ->leftJoin('user.partner', 'partner')
            ->leftJoin('partner.district', 'district');

        foreach ($filter as $key => $value) {
            switch ($key) {
                case 'id':
                    $qb->andWhere($e->eq('user.id', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'ids':
                    $qb->andWhere($e->in('user.id', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'login':
                    $qb->andWhere($e->orX()
                        ->add($e->eq('user.email', ":$key"))
                        ->add($e->eq('user.phone', ":$key"))
                    )->setParameter($key, $value);
                    break;
                case 'isActive':
                    $qb->andWhere($e->eq('user.isActive', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'location':
                    $qb->andWhere($e->eq('currentLocation.id', ":$key"))
                        ->setParameter($key, $value);
                    break;
                case 'accessToken':
                    $qb->andWhere($e->eq('user.accessToken', ":$key"))
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

        $qb->select($e->countDistinct('user.id'));

        return $qb->getQuery()
            ->useQueryCache(true)
            ->getSingleScalarResult();
    }
}