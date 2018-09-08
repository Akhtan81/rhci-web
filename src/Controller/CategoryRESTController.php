<?php

namespace App\Controller;

use App\Service\CategoryService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CategoryRESTController extends Controller
{

    public function getsV1Action(Request $request, $locale)
    {
        $filter = $request->get('filter', []);
        $trans = $this->get('translator');

        $filter['locale'] = $locale;

        if (!isset($filter['type'])) {
            return new JsonResponse([
                'message' => $trans->trans('validation.bad_request')
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $service = $this->get(CategoryService::class);
        try {

            $entities = $service->findByFilter($filter);

            $tree = $service->buildTree($entities);

            $items = $service->serialize($tree);

            return new JsonResponse([
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
        $trans = $this->get('translator');
        $userService = $this->get(UserService::class);
        $admin = $userService->getAdmin();
        $partner = $userService->getPartner();
        if (!($admin || $partner)) {
            return new JsonResponse([
                'message' => $trans->trans('validation.forbidden')
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        $trans = $this->get('translator');
        $filter = $request->get('filter', []);

        $service = $this->get(CategoryService::class);

        if (!(isset($filter['type']) && isset($filter['locale']))) {
            return new JsonResponse([
                'message' => $trans->trans('validation.bad_request')
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        try {

            $total = $service->countByFilter($filter);
            $items = [];

            if ($total > 0) {
                $entities = $service->findByFilter($filter);

                $tree = $service->buildTree($entities);

                $items = $service->serialize($tree);
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
        $admin = $this->get(UserService::class)->getAdmin();
        if (!$admin) {
            return new JsonResponse([
                'message' => $trans->trans('validation.forbidden')
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        $service = $this->get(CategoryService::class);

        try {

            $entity = $service->findOneByFilter([
                'id' => $id
            ]);
            if (!$entity) {
                throw $this->createNotFoundException();
            }

            $item = $service->serializeV2($entity);

            return new JsonResponse($item);

        } catch (\Exception $e) {

            return new JsonResponse([
                'message' => $e->getMessage()
            ], $e->getCode() > 300 ? $e->getCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function putAction(Request $request, $id)
    {
        $trans = $this->get('translator');
        $admin = $this->get(UserService::class)->getAdmin();
        if (!$admin) {
            return new JsonResponse([
                'message' => $trans->trans('validation.forbidden')
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        $content = json_decode($request->getContent(), true);

        if (!$content) {
            return new JsonResponse([
                'message' => $trans->trans('validation.bad_request')
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $em = $this->get('doctrine')->getManager();

        $service = $this->get(CategoryService::class);

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

            $item = $service->serializeV2($entity);

            return new JsonResponse($item);

        } catch (\Exception $e) {

            $em->rollback();

            return new JsonResponse([
                'message' => $e->getMessage()
            ], $e->getCode() > 300 ? $e->getCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function postAction(Request $request)
    {
        $trans = $this->get('translator');
        $admin = $this->get(UserService::class)->getAdmin();
        if (!$admin) {
            return new JsonResponse([
                'message' => $trans->trans('validation.forbidden')
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        $content = json_decode($request->getContent(), true);

        if (!$content) {
            return new JsonResponse([
                'message' => 'Missing content'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $em = $this->get('doctrine')->getManager();

        $service = $this->get(CategoryService::class);

        $em->beginTransaction();
        try {

            $entity = $service->create($content);

            $em->commit();

            $item = $service->serializeV2($entity);

            return new JsonResponse($item, JsonResponse::HTTP_CREATED);

        } catch (\Exception $e) {

            $em->rollback();

            return new JsonResponse([
                'message' => $e->getMessage()
            ], $e->getCode() > 300 ? $e->getCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteAction($id)
    {
        $trans = $this->get('translator');
        $admin = $this->get(UserService::class)->getAdmin();
        if (!$admin) {
            return new JsonResponse([
                'message' => $trans->trans('validation.forbidden')
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        $em = $this->get('doctrine')->getManager();

        $service = $this->get(CategoryService::class);

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

            $service->remove($entity);

            $em->commit();

            return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);

        } catch (\Exception $e) {

            $em->rollback();

            return new JsonResponse([
                'message' => $e->getMessage()
            ], $e->getCode() > 300 ? $e->getCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}