<?php

namespace App\Controller;

use App\Service\RegionService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class GeoRegionRESTController extends Controller
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

        $service = $this->get(RegionService::class);

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