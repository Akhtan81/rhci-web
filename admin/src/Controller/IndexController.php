<?php

namespace App\Controller;

use App\Service\PartnerSubscriptionService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class IndexController extends Controller
{
    public function index()
    {

        $subscriptionService = $this->container->get(PartnerSubscriptionService::class);
        $userService = $this->container->get(UserService::class);
        $partner = $userService->getPartner();

        $subscription = null;
        if ($partner) {
            $subscriptions = $subscriptionService->findByFilter([
                'partner' => $partner->getId(),
            ], 1, 1);
            if ($subscriptions) {
                $subscription = $subscriptions[0];
            }
        }

        return $this->render('index.html.twig', [
            'subscription' => $subscription
        ]);
    }


    public function offer()
    {
        return $this->render('offer.html.twig');
    }

    public function privacy()
    {
        return $this->render('privacy.html.twig');
    }

    public function terms()
    {
        return $this->render('terms.html.twig');
    }

    public function logout()
    {
        throw $this->createAccessDeniedException();
    }
}
