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
     * @return CreditCard
     * @throws \Exception
     */
    public function create(User $user, $content)
    {
        $entity = new CreditCard();
        $entity->setUser($user);

        $this->update($entity, $content);

        return $entity;
    }

    /**
     * @param CreditCard $entity
     * @param $content
     *
     * @throws \Exception
     */
    public function update(CreditCard $entity, $content)
    {
        $trans = $this->container->get('translator');
        $em = $this->container->get('doctrine')->getManager();

        if (isset($content['holder'])) {
            $entity->setHolder($content['holder']);
        }

        if (isset($content['code'])) {
            $entity->setCode($content['code']);
        }

        if (isset($content['cvc'])) {
            $entity->setCvc($content['cvc']);
        }

        if (isset($content['month'])) {
            $entity->setMonth($content['month']);
        }

        if (isset($content['year'])) {
            $entity->setYear($content['year']);
        }

        $match = $this->findOneByFilter([
            'code' => $entity->getCode()
        ]);
        if ($match && $match !== $entity) {
            throw new \Exception($trans->trans('validation.non_unique_credit_card'), 400);
        }

        $em->persist($entity);
        $em->flush();
    }

    /**
     * @param CreditCard $entity
     *
     * @throws \Exception
     */
    public function remove(CreditCard $entity)
    {
        $em = $this->container->get('doctrine')->getManager();

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