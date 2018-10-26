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

        $subscriptionService = $this->container->get(PartnerSubscriptionService::class);
        $partnerService = $this->container->get(PartnerService::class);
        $em = $this->container->get('doctrine')->getManager();

        $customer = $event['customer'];

        switch ($event['object']) {
            case 'invoice':

                if (isset($event['lines']['data'])) {
                    foreach ($event['lines']['data'] as $invoiceData) {
                        if (!isset($invoiceData['type'])) continue;

                        $id = $invoiceData['subscription'];

                        switch ($invoiceData['type']) {
                            case 'subscription':

                                $partner = $partnerService->findOneByFilter([
                                    'customerId' => $customer
                                ]);
                                if (!$partner) continue;

                                $startedAt = new \DateTime();
                                $startedAt->setTimestamp($invoiceData['period']['start']);

                                $finishedAt = new \DateTime();
                                $finishedAt->setTimestamp($invoiceData['period']['end']);

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

                                $subscription->setProviderResponse(json_encode($invoiceData));
                                $subscription->setStartedAt($startedAt);
                                $subscription->setFinishedAt($finishedAt);

                                $subscriptionService->update($subscription, null, false);

                                break;
                        }
                    }
                }

                break;
        }

        $em->flush();
    }

}