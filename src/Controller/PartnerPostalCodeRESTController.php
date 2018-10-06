<?php

namespace App\Controller;

use App\Service\PartnerPostalCodeService;
use App\Service\PartnerService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PartnerPostalCodeRESTController extends Controller
{

    public function findOwnerAction(Request $request)
    {
        $response = $this->denyAccessUnlessAdminOrPartner();
        if ($response) return $response;

        $trans = $this->get('translator');

        $service = $this->get(PartnerPostalCodeService::class);
        $partnerService = $this->get(PartnerService::class);

        $content = json_decode($request->getContent(), true);
        if (!isset($content['postalCodes'])) {
            return new JsonResponse([
                'message' => $trans->trans('validation.bad_request')
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        try {

            foreach ($content['postalCodes'] as &$item) {
                $partnerCode = $service->findOneByFilter([
                    'postalCode' => $item['postalCode'],
                    'type' => $item['type'],
                ]);
                if ($partnerCode) {
                    $partner = $partnerService->serializeV2($partnerCode->getPartner());

                    $item['partner'] = $partner;
                } else {
                    $item['partner'] = null;
                }
            }

            return new JsonResponse($content);

        } catch (\Exception $e) {

            return new JsonResponse([
                'message' => $e->getMessage()
            ], $e->getCode() > 300 ? $e->getCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function denyAccessUnlessAdminOrPartner()
    {
        $trans = $this->get('translator');
        $userService = $this->get(UserService::class);
        $user = $userService->getUser();
        if (!$user) {
            return new JsonResponse([
                'message' => $trans->trans('validation.unauthorized')
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $partner = $userService->getPartner();
        $admin = $userService->getAdmin();
        if (!($admin || $partner)) {
            return new JsonResponse([
                'message' => $trans->trans('validation.forbidden')
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        return null;
    }

}
