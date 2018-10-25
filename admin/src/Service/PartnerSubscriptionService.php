<?php

namespace App\Service;

use App\Entity\Partner;
use App\Entity\PartnerSubscription;
use App\Entity\SubscriptionStatus;
use App\Entity\SubscriptionType;
use JMS\Serializer\SerializationContext;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PartnerSubscriptionService
{

    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param Partner $partner
     * @param $content
     *
     * @param bool $flush
     *
     * @return PartnerSubscription
     * @throws \Exception
     */
    public function create(Partner $partner, $content = null, $flush = true)
    {
        $trans = $this->container->get('translator');

        $entity = $this->findOneByFilter([
            'partner' => $partner->getId(),
            'status' => SubscriptionStatus::ACTIVE
        ]);
        if ($entity) {
            throw new \Exception($trans->trans('validation.only_one_active_subscription_allowed'), 400);
        }

        $entity = new PartnerSubscription();
        $entity->setPartner($partner);
        $entity->setType(SubscriptionType::RECYCLING_ACCESS);

        $this->startSubscription($entity);

        $this->update($entity, $content, $flush);

        return $entity;
    }

    /**
     * @param PartnerSubscription $entity
     * @param $content
     *
     * @param bool $flush
     *
     * @throws \Exception
     */
    public function update(PartnerSubscription $entity, $content = null, $flush = true)
    {
        $em = $this->container->get('doctrine')->getManager();

        $now = new \DateTime();

        if ($entity->getStartedAt() <= $now && $now < $entity->getFinishedAt()) {
            $entity->setStatus(SubscriptionStatus::ACTIVE);
        } else {
            $entity->setStatus(SubscriptionStatus::COMPLETED);
        }

        $em->persist($entity);

        $flush && $em->flush();
    }

    public function cancel(Partner $partner)
    {
        $em = $this->container->get('doctrine')->getManager();

        $subscriptions = $this->findByFilter([
            'partner' => $partner,
            'status' => SubscriptionStatus::ACTIVE
        ]);

        if (!count($subscriptions)) return;

        $id = null;

        /** @var PartnerSubscription $subscription */
        foreach ($subscriptions as $subscription) {
            $subscription->setStatus(SubscriptionStatus::CANCELED);

            $id = $subscription->getProviderId();

            $em->persist($subscription);
        }

        $em->flush();

        if ($id) {
            $this->cancelSubscription($id);
        }
    }

    private function startSubscription(PartnerSubscription $entity)
    {
        $trans = $this->container->get('translator');
        $secret = $this->container->getParameter('stripe_client_secret');

        if ($secret) {
            try {
                \Stripe\Stripe::setApiKey($secret);

                $subscription = \Stripe\Subscription::create([
                    "customer" => $entity->getPartner()->getCustomerId(),
                    "items" => [
                        [
                            "plan" => SubscriptionType::RECYCLING_ACCESS
                        ],
                    ]
                ]);

                $response = json_encode($subscription->jsonSerialize());

                $startedAt = new \DateTime();
                $startedAt->setTimestamp($subscription->current_period_start);

                $finishedAt = new \DateTime();
                $finishedAt->setTimestamp($subscription->current_period_end);

                $entity->setProviderId($subscription->id);
                $entity->setProviderResponse($response);

                $entity->setStartedAt($startedAt);
                $entity->setFinishedAt($finishedAt);

            } catch (\Exception $e) {
                throw new \Exception($trans->trans('subscription.could_not_create', [
                    '__MSG__' => $e->getMessage()
                ]));
            }
        } else {
            $entity->setProviderId("test");
            $entity->setStartedAt(new \DateTime());

            $entity->setFinishedAt(new \DateTime());
            $entity->getFinishedAt()->modify('+30 days');

        }
    }

    private function cancelSubscription($id)
    {
        $trans = $this->container->get('translator');
        $secret = $this->container->getParameter('stripe_client_secret');

        if ($secret) {
            try {
                \Stripe\Stripe::setApiKey($secret);

                $sub = \Stripe\Subscription::retrieve($id);
                $sub->cancel();

            } catch (\Exception $e) {
                throw new \Exception($trans->trans('subscription.could_not_cancel', [
                    '__MSG__' => $e->getMessage()
                ]));
            }
        }
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

        return $em->getRepository(PartnerSubscription::class)->countByFilter($filter);
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

        return $em->getRepository(PartnerSubscription::class)->findByFilter($filter, $page, $limit);
    }

    /**
     * @param array $filter
     *
     * @return PartnerSubscription|null
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

    public function serializeV2($content)
    {
        return json_decode($this->container->get('jms_serializer')
            ->serialize($content, 'json', SerializationContext::create()
                ->setGroups(['api_v1', 'api_v2'])), true);
    }


}