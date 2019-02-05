<?php

namespace App\Controller;

use App\Service\PaymentService;
use App\Service\UserService;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PaymentRESTController extends Controller
{

    public function getsAction(Request $request)
    {
        $response = $this->denyAccessUnlessAdmin();
        if ($response) return $response;

        $filter = $request->get('filter', []);

        $page = $request->get('page', 1);
        $page = intval($page > 0 ? $page : 1);

        $limit = $request->get('limit', 10);
        $limit = intval($limit >= 0 ? $limit : 10);

        $service = $this->get(PaymentService::class);
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
                'count' => count($items),
                'total' => $total,
                'items' => $items
            ]);

        } catch (\Exception $e) {

            return new JsonResponse([
                'message' => $e->getMessage()
            ], $e->getCode() > 300 ? $e->getCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function putAction(Request $request, $id)
    {
        $response = $this->denyAccessUnlessAdmin();
        if ($response) return $response;

        $trans = $this->get('translator');

        $content = json_decode($request->getContent(), true);

        if (!$content) {
            return new JsonResponse([
                'message' => $trans->trans('validation.bad_request')
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $em = $this->get('doctrine')->getManager();

        $service = $this->get(PaymentService::class);

        $entity = $service->findOneByFilter([
            'id' => $id
        ]);
        if (!$entity) {
            return new JsonResponse([
                'message' => $trans->trans('validation.not_found')
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        /** @var Connection $con */
        $con = $em->getConnection();

        $con->beginTransaction();
        try {

            $service->update($entity, $content);

            $con->commit();

            $item = $service->serialize($entity);

            return new JsonResponse($item);

        } catch (\Exception $e) {

            if ($con->isTransactionActive()) {
                $con->rollback();
            }

            return new JsonResponse([
                'message' => $e->getMessage()
            ], $e->getCode() > 300 ? $e->getCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function denyAccessUnlessAdmin()
    {
        $trans = $this->get('translator');
        $userService = $this->get(UserService::class);

        if (!$userService->getUser()) {
            return new JsonResponse([
                'message' => $trans->trans('validation.unauthorized')
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        if (!$userService->getAdmin()) {
            return new JsonResponse([
                'message' => $trans->trans('validation.forbidden')
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        return null;
    }
}