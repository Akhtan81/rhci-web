<?php

namespace App\Controller;

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

        $em = $this->get('doctrine')->getManager();

        $service = $this->get(UserService::class);

        $em->beginTransaction();
        try {

            $entity = $service->create($content);

            $em->commit();

            $item = $service->serialize($entity);

            return new JsonResponse($item, JsonResponse::HTTP_CREATED);

        } catch (\Exception $e) {

            $em->rollback();

            return new JsonResponse([
                'message' => $e->getMessage()
            ], $e->getCode() > 300 ? $e->getCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function putAction(Request $request, $id)
    {
        $content = json_decode($request->getContent(), true);

        $service = $this->get(UserService::class);

        $em = $this->get('doctrine')->getManager();

        $user = $service->findOneByFilter([
            'id' => $id
        ]);
        if (!$user) {
            return new JsonResponse([
                'message' => 'not found'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $em->beginTransaction();
        try {

            $service->update($user, $content);

            $em->commit();

            $item = $service->serialize($user);

            return new JsonResponse($item);

        } catch (\Exception $e) {

            $em->rollback();

            return new JsonResponse([
                'message' => $e->getMessage()
            ], $e->getCode() > 300 ? $e->getCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}