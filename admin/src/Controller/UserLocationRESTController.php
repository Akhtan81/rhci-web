<?php

namespace App\Controller;

use App\Service\UserLocationService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserLocationRESTController extends Controller
{

    public function deleteAction($userId, $id)
    {
        $trans = $this->get('translator');
        $user = $this->get(UserService::class)->getUser();
        if (!$user) {
            return new JsonResponse([
                'message' => $trans->trans('validation.forbidden')
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        $service = $this->get(UserLocationService::class);

        try {
            $location = $service->findOneByFilter([
                'id' => $id,
                'user' => $userId
            ]);
            if (!$location) {
                throw new \Exception($trans->trans('validation.not_found'), 404);
            }

            $service->remove($location);

            return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);

        } catch (\Exception $e) {

            return new JsonResponse([
                'message' => $e->getMessage()
            ], $e->getCode() > 300 ? $e->getCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteForMeAction($id)
    {
        $trans = $this->get('translator');
        $user = $this->get(UserService::class)->getUser();
        if (!$user) {
            return new JsonResponse([
                'message' => $trans->trans('validation.forbidden')
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        $service = $this->get(UserLocationService::class);

        try {
            $location = $service->findOneByFilter([
                'id' => $id,
                'user' => $user->getId()
            ]);
            if (!$location) {
                throw new \Exception($trans->trans('validation.not_found'), 404);
            }

            $service->remove($location);

            return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);

        } catch (\Exception $e) {

            return new JsonResponse([
                'message' => $e->getMessage()
            ], $e->getCode() > 300 ? $e->getCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
