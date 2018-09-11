<?php

namespace App\Controller;

use App\Service\PartnerService;
use App\Service\StripeService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StripeController extends Controller
{
    public function callback(Request $request)
    {
        $authCode = $request->get('code', null);
        $partnerId = $request->get('state', null);

        if (!($partnerId && $authCode)) {
            throw $this->createAccessDeniedException();
        }

        $partner = $this->container->get(PartnerService::class)->findOneByFilter([
            'id' => $partnerId
        ]);
        if (!$partner) {
            throw $this->createNotFoundException();
        }

        $service = $this->get(StripeService::class);

        $service->updateAccountId($partner, $authCode);

        return new Response('ok');
//        return $this->redirect($this->generateUrl('profile_index'));
    }
}
