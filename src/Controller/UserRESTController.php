<?php

namespace App\Controller;

use App\Entity\Role;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UserRESTController extends Controller
{

    public function getAction($id)
    {
        $service = $this->get(UserService::class);

        try {

            $user = $service->findOneByFilter([
                'id' => $id
            ]);
            if (!$user) {
                throw $this->createNotFoundException();
            }

            $item = $service->serialize($user);

            return new JsonResponse($item);

        } catch (\Exception $e) {

            return new JsonResponse([
                'message' => $e->getMessage()
            ], $e->getCode() > 300 ? $e->getCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function postAction(Request $request)
    {
        $content = json_decode($request->getContent(), true);

        $service = $this->get(UserService::class);

        try {

            $entity = $service->create($content);

            $item = $service->serialize($entity);

            return new JsonResponse($item, JsonResponse::HTTP_CREATED);

        } catch (\Exception $e) {

            return new JsonResponse([
                'message' => $e->getMessage()
            ], $e->getCode() > 300 ? $e->getCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function putAction(Request $request, $id)
    {
        $content = json_decode($request->getContent(), true);

        $service = $this->get(UserService::class);

        try {
            $user = $service->findOneByFilter([
                'id' => $id
            ]);
            if (!$user) {
                throw $this->createNotFoundException();
            }

            $service->update($user, $content);

            $item = $service->serialize($user);

            return new JsonResponse($item);

        } catch (\Exception $e) {

            return new JsonResponse([
                'message' => $e->getMessage()
            ], $e->getCode() > 300 ? $e->getCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function meAction(Request $request)
    {
        $this->denyAccessUnlessGranted(Role::USER);

        $content = json_decode($request->getContent(), true);

        $service = $this->get(UserService::class);

        $user = $service->getUser();
        try {

            $service->update($user, $content);

            $item = $service->serialize($user);

            return new JsonResponse($item);

        } catch (\Exception $e) {

            return new JsonResponse([
                'message' => $e->getMessage()
            ], $e->getCode() > 300 ? $e->getCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}