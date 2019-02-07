<?php

namespace App\Controller;

use App\Entity\Order;
use App\Service\OrderService;
use App\Service\UserService;
use Doctrine\DBAL\Connection;
use Gedmo\SoftDeleteable\Filter\SoftDeleteableFilter;
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

        $locale = $request->getLocale();
        $service = $this->get(OrderService::class);

        $filter['user'] = $user->getId();

        try {

            $total = $service->countByFilter($filter);
            $items = [];

            if ($total > 0) {
                $entities = $service->findByFilter($filter, $page, $limit);

                $items = $service->serialize($entities, $locale);
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

    public function getsV2Action(Request $request)
    {
        $response = $this->denyAccessUnlessAdminOrPartner();
        if ($response) return $response;

        $userService = $this->get(UserService::class);

        $partner = $userService->getPartner();
        $admin = $userService->getAdmin();

        $filter = $request->get('filter', []);

        $page = $request->get('page', 1);
        $page = intval($page <= 0 ? 1 : $page);

        $limit = $request->get('limit', 10);
        $limit = intval($limit < 0 ? 10 : $limit);

        $locale = $request->getLocale();
        $service = $this->get(OrderService::class);

        if (!$admin) {
            if ($partner) {
                $accessFilter = $service->getPartnerAccessFilter();

                $filter = array_merge($filter, $accessFilter);
            }
        }

        try {

            $total = $service->countByFilter($filter);
            $items = [];

            if ($total > 0) {
                $entities = $service->findByFilter($filter, $page, $limit);

                $items = $service->serializeV2($entities, $locale);
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

    public function getsLocationsAction(Request $request)
    {
        $response = $this->denyAccessUnlessAdmin();
        if ($response) return $response;

        $locale = $request->getLocale();
        $filter = $request->get('filter', []);

        $em = $this->get('doctrine')->getManager();

        /** @var SoftDeleteableFilter $softDelete */
        $softDelete = $em->getFilters()->getFilter('softdeleteable');

        $softDelete->disableForEntity(Order::class);

        $service = $this->get(OrderService::class);

        try {

            $entities = $em->getRepository(Order::class)->findLocationsByFilter($filter);

            $items = $service->serializeV2($entities, $locale);

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

    public function getAction(Request $request, $id)
    {
        $trans = $this->get('translator');
        $user = $this->get(UserService::class)->getUser();
        if (!$user) {
            return new JsonResponse([
                'message' => $trans->trans('validation.unauthorized')
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $locale = $request->getLocale();
        $service = $this->get(OrderService::class);

        try {

            $entity = $service->findOneByFilter([
                'id' => $id,
                'user' => $user->getId()
            ]);
            if (!$entity) {
                throw new \Exception($trans->trans('validation.not_found'), 404);
            }

            $item = $service->serialize($entity, $locale);

            return new JsonResponse($item);

        } catch (\Exception $e) {

            return new JsonResponse([
                'message' => $e->getMessage()
            ], $e->getCode() > 300 ? $e->getCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getV2Action(Request $request, $id)
    {
        $response = $this->denyAccessUnlessAdminOrPartner();
        if ($response) return $response;

        $trans = $this->get('translator');

        $locale = $request->getLocale();
        $userService = $this->get(UserService::class);
        $service = $this->get(OrderService::class);

        $admin = $userService->getAdmin();

        $accessFilter = $service->getPartnerAccessFilter($id);

        $em = $this->get('doctrine')->getManager();

        if ($admin) {
            /** @var SoftDeleteableFilter $softDelete */
            $softDelete = $em->getFilters()->getFilter('softdeleteable');

            $softDelete->disableForEntity(Order::class);
        }

        try {

            $entity = $service->findOneByFilter($accessFilter);
            if (!$entity) {
                throw new \Exception($trans->trans('validation.not_found'), 404);
            }

            $item = $service->serializeV2($entity, $locale);

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

        $locale = $request->getLocale();
        $service = $this->get(OrderService::class);
        $em = $this->get('doctrine')->getManager();

        $em->beginTransaction();
        try {

            $entity = $service->create($content);

            $em->commit();

            $item = $service->serialize($entity, $locale);

            return new JsonResponse($item, JsonResponse::HTTP_CREATED);

        } catch (\Exception $e) {

            /** @var Connection $con */
            $con = $em->getConnection();
            if ($con->isTransactionActive()) {
                $em->rollback();
            }

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

        $locale = $request->getLocale();
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

            $item = $service->serialize($order, $locale);

            return new JsonResponse($item);

        } catch (\Exception $e) {

            /** @var Connection $con */
            $con = $em->getConnection();
            if ($con->isTransactionActive()) {
                $em->rollback();
            }

            return new JsonResponse([
                'message' => $e->getMessage()
            ], $e->getCode() > 300 ? $e->getCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function putV2Action(Request $request, $id)
    {
        $response = $this->denyAccessUnlessAdminOrPartner();
        if ($response) return $response;

        $trans = $this->get('translator');
        $em = $this->get('doctrine')->getManager();
        $service = $this->get(OrderService::class);

        $locale = $request->getLocale();
        $accessFilter = $service->getPartnerAccessFilter($id);

        $order = $service->findOneByFilter($accessFilter);
        if (!$order) {
            return new JsonResponse([
                'message' => $trans->trans('validation.not_found')
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $content = json_decode($request->getContent(), true);

        $em->beginTransaction();
        try {

            $service->update($order, $content);

            $em->commit();

            $item = $service->serializeV2($order, $locale);

            return new JsonResponse($item);

        } catch (\Exception $e) {

            /** @var Connection $con */
            $con = $em->getConnection();
            if ($con->isTransactionActive()) {
                $em->rollback();
            }

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

    private function denyAccessUnlessAdmin()
    {
        $trans = $this->get('translator');
        $userService = $this->get(UserService::class);
        $user = $userService->getUser();
        if (!$user) {
            return new JsonResponse([
                'message' => $trans->trans('validation.unauthorized')
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $admin = $userService->getAdmin();
        if (!$admin) {
            return new JsonResponse([
                'message' => $trans->trans('validation.forbidden')
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        return null;
    }
}
