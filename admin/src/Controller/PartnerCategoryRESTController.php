<?php

namespace App\Controller;

use App\Entity\CategoryType;
use App\Entity\Partner;
use App\Entity\PartnerCategory;
use App\Entity\PartnerPostalCode;
use App\Entity\PartnerStatus;
use App\Service\CategoryService;
use App\Service\PartnerCategoryService;
use App\Service\PartnerService;
use App\Service\UserService;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PartnerCategoryRESTController extends Controller
{

    public function getsV1(Request $request, $locale = null)
    {
        $em = $this->get('doctrine')->getManager();
        $trans = $this->get('translator');
        $service = $this->get(PartnerCategoryService::class);
        $partnerService = $this->get(PartnerService::class);

        $filter = $request->get('filter', []);

        if (!(isset($filter['postalCode']) && isset($filter['country']))) {
            return new JsonResponse([
                'message' => $trans->trans('validation.bad_request')
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (!$locale) {
            $locale = $request->getLocale();
        }

        $requestedPostalCode = trim($filter['postalCode']);

        try {

            $postalCodes = $em->getRepository(PartnerPostalCode::class)->findByFilter([
                'postalCode' => $requestedPostalCode,
            ]);

            $partnerIds = array_map(function (PartnerPostalCode $code) {
                return $code->getPartner()->getId();
            }, $postalCodes);

            if (!$partnerIds) {
                throw new \Exception($trans->trans('validation.no_partner_category_found'), 404);
            }

            $partnerIds = $em->getRepository(Partner::class)->findIdsByFilter([
                'status' => PartnerStatus::APPROVED,
                'canManagerOrders' => true,
                'ids' => $partnerIds,
                'countryName' => $filter['country'],
            ]);

            if (!$partnerIds) {
                throw new \Exception($trans->trans('validation.no_partner_category_found'), 404);
            }

            $partnerPostalCodes = $em->getRepository(PartnerPostalCode::class)->findPostalCodesByPartner([
                'postalCode' => $requestedPostalCode,
                'partners' => $partnerIds
            ]);

            $postalCodePerPartner = [];

            foreach ($partnerPostalCodes as $postalCodeArr) {

                $partnerId = $postalCodeArr['id'];
                $type = $postalCodeArr['type'];

                if (!isset($postalCodePerPartner[$partnerId])) {
                    $postalCodePerPartner[$partnerId] = [
                        CategoryType::JUNK_REMOVAL => [],
                        CategoryType::RECYCLING => [],
                        CategoryType::DONATION => [],
                        CategoryType::SHREDDING => [],
                    ];
                }

                $postalCodePerPartner[$partnerId][$type][] = $postalCodeArr['postalCode'];
            }

            $partnerCategories = $em->getRepository(PartnerCategory::class)->findCategoriesAndPartnersByFilter([
                'locale' => $locale,
                'partners' => $partnerIds
            ]);

            $categoryPerPartner = [];
            $partners = [];

            /** @var PartnerCategory $partnerCategory */
            foreach ($partnerCategories as $partnerCategory) {

                $id = $partnerCategory->getPartner()->getId();
                $type = $partnerCategory->getCategory()->getType();

                $partners[$id] = $partnerCategory->getPartner();

                if (!isset($categoryPerPartner[$id])) {
                    $categoryPerPartner[$id] = [];
                }

                if (isset($postalCodePerPartner[$id][$type]) && in_array($requestedPostalCode, $postalCodePerPartner[$id][$type])) {
                    $categoryPerPartner[$id][] = $partnerCategory;
                }
            }

            $items = $partnerService->serialize($partners, $locale);

            $response = [];

            foreach ($items as $partner) {
                $categories = [];
                $id = $partner['id'];

                unset($partner['requests']);
                unset($partner['requestedCategories']);
                unset($partner['postalCodes']);
                unset($partner['user']['locations']);

                if (isset($categoryPerPartner[$id])) {

                    $categories = array_filter($categoryPerPartner[$id], function (PartnerCategory $partnerCategory) use ($partner) {
                        switch ($partnerCategory->getCategory()->getType()) {
                            case CategoryType::DONATION:
                                return $partner['canManageDonationOrders'];
                            case CategoryType::JUNK_REMOVAL:
                                return $partner['canManageJunkRemovalOrders'];
                            case CategoryType::SHREDDING:
                                return $partner['canManageShreddingOrders'];
                            case CategoryType::RECYCLING:
                                return $partner['canManageRecyclingOrders'];
                        }

                        return false;
                    });

                    $tree = $service->buildTree($categories);

                    $categories = $service->serialize($tree, $locale);
                }

                if (count($categories) > 0) {
                    $response[] = [
                        'partner' => $partner,
                        'categories' => $categories
                    ];
                }
            }

            if (count($response) === 0) {
                throw new \Exception($trans->trans('validation.no_partner_category_found'), 404);
            }

            return new JsonResponse([
                'count' => count($response),
                'items' => $response
            ]);

        } catch (\Exception $e) {

            return new JsonResponse([
                'message' => $e->getMessage()
            ], $e->getCode() > 300 ? $e->getCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getsAction(Request $request)
    {
        $response = $this->denyAccessUnlessPartner();
        if ($response) return $response;

        $trans = $this->get('translator');
        $partner = $this->get(UserService::class)->getPartner();
        $service = $this->get(PartnerCategoryService::class);
        $locale = $request->getLocale();

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

                $tree = $service->buildTree($entities);

                $items = $service->serializeV2($tree, $locale);
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

    public function getAction(Request $request, $id)
    {
        $trans = $this->get('translator');
        $partner = $this->get(UserService::class)->getPartner();
        if (!$partner) {
            return new JsonResponse([
                'message' => $trans->trans('validation.forbidden')
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        $locale = $request->getLocale();

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

            $item = $service->serializeV2($entity, $locale);

            $children = $service->countByFilter([
                'parent' => $entity->getCategory()->getId()
            ]);

            $item['hasChildren'] = $children > 0;

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

        $locale = $request->getLocale();
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

            $item = $service->serializeV2($entity, $locale);

            $children = $service->countByFilter([
                'parent' => $entity->getCategory()->getId()
            ]);

            $item['hasChildren'] = $children > 0;

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

    public function remove($id)
    {
        $response = $this->denyAccessUnlessPartner();
        if ($response) return $response;

        $trans = $this->get('translator');
        $em = $this->get('doctrine')->getManager();
        $service = $this->get(PartnerCategoryService::class);
        $partner = $this->get(UserService::class)->getPartner();

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

            $service->remove($entity);

            $em->commit();

            return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);

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

        $locale = $request->getLocale();
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

            $item = $service->serializeV2($entity, $locale);

            $children = $service->countByFilter([
                'parent' => $entity->getCategory()->getId(),
            ]);

            $item['hasChildren'] = $children > 0;

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