<?php

namespace App\Controller;

use App\Service\PartnerService;
use App\Service\PaymentService;
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

        $service = $this->container->get(PartnerService::class);
        $paymentService = $this->get(PaymentService::class);

        $partner = $service->findOneByFilter([
            'id' => $partnerId
        ]);
        if (!$partner) {
            throw $this->createNotFoundException();
        }

        try {
            $paymentService->updateAccountId($partner, $authCode);

            return $this->redirect($this->generateUrl('profile_index'));
        } catch (\Exception $e) {
            return new Response(
                $e->getMessage(),
                $e->getCode() > 300 ? $e->getCode() : Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
