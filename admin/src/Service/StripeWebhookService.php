<?php

namespace App\Service;

use App\Entity\PartnerSubscription;
use App\Entity\SubscriptionType;
use Symfony\Component\DependencyInjection\ContainerInterface;

class StripeWebhookService
{

    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function handleEvent($event)
    {
        if (!isset($event['object'])) return;

        $em = $this->container->get('doctrine')->getManager();

        switch ($event['object']) {
            case 'subscription':
                $id = $event['$id'];

                $this->handleSubscription($id, $event);

                break;
            case 'invoice':

                if (isset($event['lines']['data'])) {
                    foreach ($event['lines']['data'] as $content) {

                        if (!isset($content['type'])) continue;

                        switch ($content['type']) {
                            case 'subscription':
                                $id = $content['subscription'];

                                $this->handleSubscription($id, $content);

                                break;
                        }
                    }
                }

                break;
        }

        $em->flush();
    }

    private function handleSubscription($id, $content) {

        $subscriptionService = $this->container->get(PartnerSubscriptionService::class);
        $partnerService = $this->container->get(PartnerService::class);

        $customer = $content['customer'];

        $partner = $partnerService->findOneByFilter([
            'customerId' => $customer
        ]);
        if (!$partner) return;

        $startedAt = new \DateTime();
        $startedAt->setTimestamp($content['period']['start']);

        $finishedAt = new \DateTime();
        $finishedAt->setTimestamp($content['period']['end']);

        $subscription = $subscriptionService->findOneByFilter([
            'providerId' => $id,
            'partner' => $partner->getId()
        ]);

        if (!$subscription) {
            $subscription = new PartnerSubscription();
            $subscription->setProviderId($id);
            $subscription->setPartner($partner);
            $subscription->setType(SubscriptionType::RECYCLING_ACCESS);
        }

        $subscription->setProviderResponse(json_encode($content));
        $subscription->setStartedAt($startedAt);
        $subscription->setFinishedAt($finishedAt);

        $subscriptionService->update($subscription, null, false);
    }

}