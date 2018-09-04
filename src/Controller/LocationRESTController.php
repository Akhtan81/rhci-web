<?php

namespace App\Controller;

use App\Entity\Role;
use App\Service\UserService;
use App\Service\LocationService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class LocationRESTController extends Controller
{

    public function deleteAction($userId, $id)
    {
        $this->denyAccessUnlessGranted(Role::USER);

        $service = $this->get(LocationService::class);

        try {
            $location = $service->findOneByFilter([
                'id' => $id,
                'user' => $userId
            ]);
            if (!$location) {
                throw $this->createNotFoundException();
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
        $this->denyAccessUnlessGranted(Role::USER);

        $service = $this->get(LocationService::class);
        $user = $this->get(UserService::class)->getUser();

        try {
            $location = $service->findOneByFilter([
                'id' => $id,
                'user' => $user->getId()
            ]);
            if (!$location) {
                throw $this->createNotFoundException();
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
