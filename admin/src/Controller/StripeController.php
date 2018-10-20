<?php

namespace App\Controller;

use App\Service\PartnerService;
use App\Service\PaymentService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class StripeController extends Controller
{
    public function callback(Request $request)
    {
        $authCode = $request->get('code', null);
        $partnerId = $request->get('state', null);

        if (!($partnerId && $authCode)) {
            throw $this->createAccessDeniedException();
        }

        $service = $this->container->get(PartnerService::class);
        $paymentService = $this->get(PaymentService::class);

        $partner = $service->findOneByFilter([
            'id' => $partnerId
        ]);
        if (!$partner) {
            throw $this->createNotFoundException();
        }

        $paymentService->updateAccountId($partner, $authCode);

        return $this->redirect($this->generateUrl('profile_index'));
    }
}
