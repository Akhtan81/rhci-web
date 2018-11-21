<?php

namespace App\Controller;

use App\Service\CategoryService;
use App\Service\PartnerCategoryService;
use App\Service\UserService;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PartnerCategoryRESTController extends Controller
{

    public function getsAction(Request $request)
    {
        $trans = $this->get('translator');
        $partner = $this->get(UserService::class)->getPartner();
        if (!$partner) {
            return new JsonResponse([
                'message' => $trans->trans('validation.forbidden')
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        $service = $this->get(PartnerCategoryService::class);

        $filter = $request->get('filter', []);

        if (!(isset($filter['type']) && isset($filter['locale']))) {
            return new JsonResponse([
                'message' => $trans->trans('validation.bad_request')
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $filter['partner'] = $partner->getId();

        try {

            $total = $service->countByFilter($filter);
            $items = [];

            if ($total > 0) {
                $entities = $service->findByFilter($filter);

                $items = $service->serialize($entities);
            }

            return new JsonResponse([
                'total' => $total,
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
        $partner = $this->get(UserService::class)->getPartner();
        if (!$partner) {
            return new JsonResponse([
                'message' => $trans->trans('validation.forbidden')
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        $service = $this->get(PartnerCategoryService::class);
        $partner = $this->get(UserService::class)->getPartner();

        try {

            $entity = $service->findOneByFilter([
                'id' => $id,
                'partner' => $partner->getId()
            ]);
            if (!$entity) {
                throw new \Exception($trans->trans('validation.not_found'), 404);
            }

            $item = $service->serializeV2($entity);

            return new JsonResponse($item);

        } catch (\Exception $e) {

            return new JsonResponse([
                'message' => $e->getMessage()
            ], $e->getCode() > 300 ? $e->getCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function put(Request $request, $id)
    {
        $response = $this->denyAccessUnlessPartner();
        if ($response) return $response;

        $trans = $this->get('translator');
        $em = $this->get('doctrine')->getManager();
        $service = $this->get(PartnerCategoryService::class);
        $partner = $this->get(UserService::class)->getPartner();

        $content = json_decode($request->getContent(), true);

        if (!$content) {
            return new JsonResponse([
                'message' => $trans->trans('validation.bad_request')
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $entity = $service->findOneByFilter([
            'id' => $id,
            'partner' => $partner->getId()
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

            $item = $service->serializeV2($entity);

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

    public function post(Request $request)
    {
        $response = $this->denyAccessUnlessPartner();
        if ($response) return $response;

        $trans = $this->get('translator');
        $em = $this->get('doctrine')->getManager();
        $categoryService = $this->get(CategoryService::class);
        $service = $this->get(PartnerCategoryService::class);
        $partner = $this->get(UserService::class)->getPartner();

        $content = json_decode($request->getContent(), true);

        if (!$content) {
            return new JsonResponse([
                'message' => $trans->trans('validation.bad_request')
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $category = $categoryService->findOneByFilter([
            'id' => $content['category'],
        ]);
        if (!$category) {
            return new JsonResponse([
                'message' => $trans->trans('validation.not_found')
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $em->beginTransaction();
        try {

            $entity = $service->create($partner, $category, $content);

            $em->commit();

            $item = $service->serializeV2($entity);

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

    private function denyAccessUnlessPartner()
    {
        $trans = $this->get('translator');
        $userService = $this->get(UserService::class);

        if (!$userService->getUser()) {
            return new JsonResponse([
                'message' => $trans->trans('validation.unauthorized')
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        if (!$userService->getPartner()) {
            return new JsonResponse([
                'message' => $trans->trans('validation.forbidden')
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        return null;
    }

}