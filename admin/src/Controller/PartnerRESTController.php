<?php

namespace App\Controller;

use App\Service\PartnerService;
use App\Service\UserService;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PartnerRESTController extends Controller
{
    public function getsAction(Request $request)
    {
        $response = $this->denyAccessUnlessAdmin();
        if ($response) return $response;

        $locale = $request->getLocale();
        $filter = $request->get('filter', []);

        $page = $request->get('page', 1);
        $page = intval($page > 0 ? $page : 1);

        $limit = $request->get('limit', 10);
        $limit = intval($limit >= 0 ? $limit : 10);

        $service = $this->get(PartnerService::class);

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

    public function getAction(Request $request, $id)
    {
        $response = $this->denyAccessUnlessAdmin();
        if ($response) return $response;

        $locale = $request->getLocale();
        $trans = $this->get('translator');

        $service = $this->get(PartnerService::class);

        try {

            $entity = $service->findOneByFilter([
                'id' => $id
            ]);
            if (!$entity) {
                throw new \Exception($trans->trans('validation.not_found'), 404);
            }

            $item = $service->serializeV2($entity, $locale);

            $service->fetchAndSerializeCollections($item, $locale);

            return new JsonResponse($item);

        } catch (\Exception $e) {

            return new JsonResponse([
                'message' => $e->getMessage()
            ], $e->getCode() > 300 ? $e->getCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getMeAction(Request $request)
    {
        $response = $this->denyAccessUnlessPartner();
        if ($response) return $response;

        $locale = $request->getLocale();

        $trans = $this->get('translator');

        $service = $this->get(PartnerService::class);
        $partner = $this->get(UserService::class)->getPartner();

        try {

            $entity = $service->findOneByFilter([
                'id' => $partner->getId()
            ]);
            if (!$entity) {
                throw new \Exception($trans->trans('validation.not_found'), 404);
            }

            $item = $service->serialize($entity, $locale, ['api_v2', 'api_v2_partner']);

            $service->fetchAndSerializeCollections($item, $locale);

            return new JsonResponse($item);

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

        $locale = $request->getLocale();
        $content = json_decode($request->getContent(), true);

        $trans = $this->get('translator');
        $em = $this->get('doctrine')->getManager();

        $service = $this->get(PartnerService::class);

        $entity = $service->findOneByFilter([
            'id' => $id
        ]);
        if (!$entity) {
            return new JsonResponse([
                'message' => $trans->trans('validation.not_found')
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $em->beginTransaction();
        try {

            $service->update($entity, $content);

            $em->commit();

            $item = $service->serializeV2($entity, $locale);

            $service->fetchAndSerializeCollections($item, $locale);

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

    public function putMeAction(Request $request)
    {
        $response = $this->denyAccessUnlessPartner();
        if ($response) return $response;

        $content = json_decode($request->getContent(), true);

        $locale = $request->getLocale();
        $trans = $this->get('translator');
        $em = $this->get('doctrine')->getManager();

        $service = $this->get(PartnerService::class);
        $partner = $this->get(UserService::class)->getPartner();

        $entity = $service->findOneByFilter([
            'id' => $partner->getId()
        ]);
        if (!$entity) {
            return new JsonResponse([
                'message' => $trans->trans('validation.not_found')
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $em->beginTransaction();
        try {

            $service->update($entity, $content);

            $em->commit();

            $item = $service->serialize($entity, $locale, ['api_v2', 'api_v2_partner']);

            $service->fetchAndSerializeCollections($item, $locale);

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

    public function postAction(Request $request)
    {
        $response = $this->denyAccessUnlessAdmin();
        if ($response) return $response;

        $locale = $request->getLocale();
        $content = json_decode($request->getContent(), true);

        $em = $this->get('doctrine')->getManager();

        $service = $this->get(PartnerService::class);

        $em->beginTransaction();
        try {

            $entity = $service->create($content);

            $em->commit();

            $item = $service->serializeV2($entity, $locale);

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

    public function postSignUpAction(Request $request)
    {
        $content = json_decode($request->getContent(), true);

        $em = $this->get('doctrine')->getManager();

        $locale = $request->getLocale();

        $service = $this->get(PartnerService::class);

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

    private function denyAccessUnlessPartner()
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
        if (!$partner) {
            return new JsonResponse([
                'message' => $trans->trans('validation.forbidden')
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        return null;
    }
}