<?php

namespace App\Controller;

use App\Entity\Role;
use App\Service\CreditCardService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CreditCardRESTController extends Controller
{

    public function getsAction()
    {
        $trans = $this->get('translator');
        $user = $this->get(UserService::class)->getUser();
        if (!$user) {
            return new JsonResponse([
                'message' => $trans->trans('validation.forbidden')
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        $service = $this->get(CreditCardService::class);

        try {

            $entities = $service->findByFilter([
                'user' => $user->getId()
            ]);

            $items = $service->serialize($entities);

            return new JsonResponse([
                'count' => count($items),
                'items' => $items
            ]);

        } catch (\Exception $e) {

            return new JsonResponse([
                'message' => $e->getMessage()
            ], $e->getCode() > 300 ? $e->getCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getAction($id)
    {
        $trans = $this->get('translator');
        $user = $this->get(UserService::class)->getUser();
        if (!$user) {
            return new JsonResponse([
                'message' => $trans->trans('validation.forbidden')
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        $service = $this->get(CreditCardService::class);

        try {

            $entity = $service->findByFilter([
                'id' => $id,
                'user' => $user->getId()
            ]);
            if (!$entity) {
                throw $this->createNotFoundException();
            }

            $item = $service->serialize($entity);

            return new JsonResponse($item);

        } catch (\Exception $e) {

            return new JsonResponse([
                'message' => $e->getMessage()
            ], $e->getCode() > 300 ? $e->getCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function postAction(Request $request)
    {
        $this->denyAccessUnlessGranted(Role::USER);

        $content = json_decode($request->getContent(), true);

        $service = $this->get(CreditCardService::class);
        $user = $this->get(UserService::class)->getUser();

        try {

            $entity = $service->create($user, $content);

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
        $this->denyAccessUnlessGranted(Role::USER);

        $content = json_decode($request->getContent(), true);

        $service = $this->get(CreditCardService::class);
        $user = $this->get(UserService::class)->getUser();

        try {

            $entity = $service->findOneByFilter([
                'id' => $id,
                'user' => $user->getId()
            ]);
            if (!$entity) {
                throw $this->createNotFoundException();
            }

            $service->update($entity, $content);

            $item = $service->serialize($entity);

            return new JsonResponse($item);

        } catch (\Exception $e) {

            return new JsonResponse([
                'message' => $e->getMessage()
            ], $e->getCode() > 300 ? $e->getCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteAction($id)
    {
        $this->denyAccessUnlessGranted(Role::USER);

        $service = $this->get(CreditCardService::class);
        $user = $this->get(UserService::class)->getUser();

        try {

            $entity = $service->findOneByFilter([
                'id' => $id,
                'user' => $user->getId()
            ]);
            if (!$entity) {
                throw $this->createNotFoundException();
            }

            $service->remove($entity);

            return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);

        } catch (\Exception $e) {

            return new JsonResponse([
                'message' => $e->getMessage()
            ], $e->getCode() > 300 ? $e->getCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}