<?php

namespace App\Controller;

use App\Service\OrderService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class OrderRESTController extends Controller
{
    public function getsAction(Request $request)
    {
        $trans = $this->get('translator');
        $user = $this->get(UserService::class)->getUser();
        if (!$user) {
            return new JsonResponse([
                'message' => $trans->trans('validation.unauthorized')
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $filter = $request->get('filter', []);
        $page = $request->get('page', 1);
        $page = intval($page <= 0 ? 1 : $page);
        $limit = $request->get('limit', 10);
        $limit = intval($limit < 0 ? 10 : $limit);

        $service = $this->get(OrderService::class);

        $filter['user'] = $user->getId();

        try {

            $total = $service->countByFilter($filter);
            $items = [];

            if ($total > 0) {
                $entities = $service->findByFilter($filter, $page, $limit);

                $items = $service->serialize($entities);
            }

            return new JsonResponse([
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
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
                'message' => $trans->trans('validation.unauthorized')
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $service = $this->get(OrderService::class);

        try {

            $entity = $service->findOneByFilter([
                'id' => $id,
                'user' => $user->getId()
            ]);
            if (!$entity) {
                throw new \Exception($trans->trans('validation.not_found'), 404);
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
        $trans = $this->get('translator');
        $user = $this->get(UserService::class)->getUser();
        if (!$user) {
            return new JsonResponse([
                'message' => $trans->trans('validation.unauthorized')
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $content = json_decode($request->getContent(), true);

        $service = $this->get(OrderService::class);
        $em = $this->get('doctrine')->getManager();

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
        $trans = $this->get('translator');
        $em = $this->get('doctrine')->getManager();
        $service = $this->get(OrderService::class);
        $userService = $this->get(UserService::class);

        $accessFilter = [
            'id' => $id
        ];

        $admin = $userService->getAdmin();
        if (!$admin) {
            $user = $userService->getUser();
            if (!$user) {
                return new JsonResponse([
                    'message' => $trans->trans('validation.unauthorized')
                ], JsonResponse::HTTP_UNAUTHORIZED);
            }

            $accessFilter['user'] = $user->getId();
        }

        $content = json_decode($request->getContent(), true);

        $order = $service->findOneByFilter($accessFilter);
        if (!$order) {
            return new JsonResponse([
                'message' => $trans->trans('validation.not_found')
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $em->beginTransaction();
        try {

            $service->update($order, $content);

            $em->commit();

            $item = $service->serialize($order);

            return new JsonResponse($item);

        } catch (\Exception $e) {

            $em->rollback();

            return new JsonResponse([
                'message' => $e->getMessage()
            ], $e->getCode() > 300 ? $e->getCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}