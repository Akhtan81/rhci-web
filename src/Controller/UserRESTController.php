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
        $trans = $this->get('translator');
        $service = $this->get(UserService::class);
        $user = $service->getUser();
        $admin = $service->getAdmin();
        if (!$admin) {
            if (!$user) {
                return new JsonResponse([
                    'message' => $trans->trans('validation.unauthorized')
                ], JsonResponse::HTTP_FORBIDDEN);
            }

            if ($user->getId() !== $id) {
                return new JsonResponse([
                    'message' => $trans->trans('validation.forbidden')
                ], JsonResponse::HTTP_FORBIDDEN);
            }
        }

        try {

            $user = $service->findOneByFilter([
                'id' => $id
            ]);
            if (!$user) {
                throw new \Exception($trans->trans('validation.not_found'), 404);
            }

            $item = $service->serialize($user);

            return new JsonResponse($item);

        } catch (\Exception $e) {

            return new JsonResponse([
                'message' => $e->getMessage()
            ], $e->getCode() > 300 ? $e->getCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getMeAction()
    {
        $trans = $this->get('translator');
        $service = $this->get(UserService::class);
        $user = $service->getUser();
        if (!$user) {
            return new JsonResponse([
                'message' => $trans->trans('validation.unauthorized')
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $user = $service->getUser();

        try {

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
        $trans = $this->get('translator');
        $service = $this->get(UserService::class);
        $user = $service->getUser();
        $admin = $service->getAdmin();
        if (!$admin) {
            if (!$user) {
                return new JsonResponse([
                    'message' => $trans->trans('validation.unauthorized')
                ], JsonResponse::HTTP_UNAUTHORIZED);
            }

            if ($user->getId() !== $id) {
                return new JsonResponse([
                    'message' => $trans->trans('validation.forbidden')
                ], JsonResponse::HTTP_FORBIDDEN);
            }
        }

        $content = json_decode($request->getContent(), true);

        try {
            $user = $service->findOneByFilter([
                'id' => $id
            ]);
            if (!$user) {
                throw new \Exception($trans->trans('validation.not_found'), 404);
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

    public function putMeAction(Request $request)
    {
        $trans = $this->get('translator');
        $service = $this->get(UserService::class);
        $user = $service->getUser();
        if (!$user) {
            return new JsonResponse([
                'message' => $trans->trans('validation.unauthorized')
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $content = json_decode($request->getContent(), true);

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