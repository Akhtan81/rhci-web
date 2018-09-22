<?php

namespace App\Service;

use App\Entity\CreditCard;
use App\Entity\User;
use JMS\Serializer\SerializationContext;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CreditCardService
{

    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param User $user
     * @param $content
     *
     * @param bool $flush
     *
     * @return CreditCard
     * @throws \Exception
     */
    public function create(User $user, $content, $flush = true)
    {
        $entity = new CreditCard();
        $entity->setUser($user);

        $this->update($entity, $content, $flush);

        return $entity;
    }

    /**
     * @param CreditCard $entity
     * @param $content
     *
     * @param bool $flush
     *
     * @throws \Exception
     */
    public function update(CreditCard $entity, $content, $flush = true)
    {
        $trans = $this->container->get('translator');
        $em = $this->container->get('doctrine')->getManager();

        if (isset($content['name'])) {
            $entity->setName($content['name']);
        }

        if (isset($content['token'])) {
            $entity->setToken($content['token']);
        }

        if (isset($content['provider'])) {
            $entity->setProvider($content['provider']);
        }

        if (isset($content['currency'])) {
            $entity->setCurrency($content['currency']);
        }

        if (isset($content['type'])) {
            $entity->setType($content['type']);
        }

        if (isset($content['isPrimary'])) {
            $entity->setIsPrimary($content['isPrimary'] === true);

            $user = $entity->getUser();

            if ($entity->isPrimary()) {
                $em->getConnection()
                    ->prepare('UPDATE credit_cards SET is_primary = FALSE WHERE user_id = ' . $user->getId())
                    ->execute();
            }

            if ($user->getPrimaryCreditCard() === $entity) {

                if (!$entity->isPrimary()) {
                    $user->setPrimaryCreditCard(null);
                } else {
                    $user->setPrimaryCreditCard($entity);
                }

                $em->persist($user);
            }
        }

        $match = $this->findOneByFilter([
            'token' => $entity->getToken()
        ]);
        if ($match && $match !== $entity) {
            throw new \Exception($trans->trans('validation.non_unique_credit_card'), 400);
        }

        $em->persist($entity);

        $flush && $em->flush();
    }

    /**
     * @param CreditCard $entity
     *
     * @throws \Exception
     */
    public function remove(CreditCard $entity)
    {
        $em = $this->container->get('doctrine')->getManager();
        $userService = $this->container->get(UserService::class);

        $user = $userService->findOneByFilter([
            'primaryCreditCard' => $entity->getId()
        ]);
        if ($user) {
            $user->setPrimaryCreditCard(null);

            $em->persist($user);
        }

        $em->remove($entity);
        $em->flush();
    }

    /**
     * @param array $filter
     *
     * @return int
     * @throws \Exception
     */
    public function countByFilter(array $filter = [])
    {
        $em = $this->container->get('doctrine')->getManager();

        return $em->getRepository(CreditCard::class)->countByFilter($filter);
    }

    /**
     * @param array $filter
     * @param int $page
     * @param int $limit
     *
     * @return array
     */
    public function findByFilter(array $filter = [], $page = 0, $limit = 0)
    {
        $em = $this->container->get('doctrine')->getManager();

        return $em->getRepository(CreditCard::class)->findByFilter($filter, $page, $limit);
    }

    /**
     * @param array $filter
     *
     * @return CreditCard|null
     */
    public function findOneByFilter(array $filter = [])
    {
        $items = $this->findByFilter($filter, 1, 1);
        if (count($items) !== 1) return null;

        return $items[0];
    }

    public function serialize($content)
    {
        return json_decode($this->container->get('jms_serializer')
            ->serialize($content, 'json', SerializationContext::create()
                ->setGroups(['api_v1'])), true);
    }


}