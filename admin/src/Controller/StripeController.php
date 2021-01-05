<?php

namespace App\Controller;

use App\Service\UserService;
use App\Service\PartnerService;
use App\Service\PaymentService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

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
        $userService = $this->container->get(UserService::class);
        $paymentService = $this->get(PaymentService::class);

        $user = $userService->findOneByFilter([
            'id' => $partnerId //partner id is same as user id
        ]);
        $partner = $service->findOneByFilter([
            'id' => $partnerId
        ]);

        if (!$partner && !$user) {
            throw $this->createNotFoundException();
        }else{
            try {
                if($partner){
                    $paymentService->updateAccountId($partner, $authCode);
                }else{
                    $paymentService->updateAccountIdForUser($user, $authCode);
                }
                return $this->redirect($this->generateUrl('profile_index'));
            } catch (\Exception $e) {
                return new Response(
                    $e->getMessage(),
                    $e->getCode() > 300 ? $e->getCode() : Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }
        }
    }

    public function postEphemeral(Request $request)
    {
        $response = $this->denyAccessUnlessAuthenticated();
        if ($response) return $response;

        $trans = $this->get('translator');

        $isEnabled = $this->container->getParameter('stripe_enabled');
        if (!$isEnabled) {
            return new JsonResponse([
                'message' => $trans->trans('validation.stripe_is_disabled')
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $content = json_decode($request->getContent(), true);

        $user = $this->get(UserService::class)->getUser();

        $secret = $this->container->getParameter('stripe_client_secret');

        $id = $user->getCustomerId();

        try {

            if (!isset($content['api_version'])) {
                throw new \Exception($trans->trans('validation.bad_request'), 400);
            }

            if (!($id && $secret)) {
                throw new \Exception($trans->trans('validation.not_found'), 404);
            }

            \Stripe\Stripe::setApiKey($secret);

            $key = \Stripe\EphemeralKey::create(
                ["customer" => $id],
                ["stripe_version" => $content['api_version']]
            );

            return new JsonResponse($key, JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => $e->getMessage()
            ], $e->getCode() > 300 ? $e->getCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function denyAccessUnlessAuthenticated()
    {
        $trans = $this->get('translator');
        $user = $this->get(UserService::class)->getUser();
        if (!$user) {
            return new JsonResponse([
                'message' => $trans->trans('validation.unauthorized')
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        return null;
    }
}
