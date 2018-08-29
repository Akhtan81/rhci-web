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
        $filter = $request->get('filter', []);
        $page = $request->get('page', 1);
        $page = intval($page <= 0 ? 1 : $page);
        $limit = $request->get('limit', 10);
        $limit = intval($limit < 0 ? 10 : $limit);

        $service = $this->get(OrderService::class);
        $user = $this->get(UserService::class)->getUser();

        $filter['user'] =  $user->getId();

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
        $service = $this->get(OrderService::class);
        $user = $this->get(UserService::class)->getUser();

        try {

            $entity = $service->findOneByFilter([
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
        $content = json_decode($request->getContent(), true);

        $service = $this->get(OrderService::class);

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
}