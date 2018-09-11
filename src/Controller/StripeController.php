<?php

namespace App\Controller;

use App\Service\StripeService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class StripeController extends Controller
{
    public function callback(Request $request)
    {
        $authCode = $request->get('code', null);
        $partner = $this->container->get(UserService::class)->getPartner();
        if (!$partner) {
            throw $this->createAccessDeniedException();
        }

        $service = $this->get(StripeService::class);

        $service->updateAccountId($partner, $authCode);

        return $this->render('stripe-callback.html.twig');
    }
}
