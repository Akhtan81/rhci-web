<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\CategoryType;
use Symfony\Component\HttpFoundation\Request;

class OrderTypeRESTController extends Controller
{

    public function getsAction(Request $request, $locale = null)
    {
        try {

            if (!$locale) {
                $locale = $request->getLocale();
            }

            $trans = $this->get('translator');

            

            $items = [
                [
                    'key' => CategoryType::JUNK_REMOVAL,
                    'name' => $trans->trans('order_types.junk_removal', [], 'messages', $locale),
                    'bidirectional' => false,
                    'disabled' => false
                ],
                [
                    'key' => CategoryType::RECYCLING,
                    'name' => $trans->trans('order_types.recycling', [], 'messages', $locale),
                    'bidirectional' => true,
                    'disabled' => false
                ],
                [
                    'key' => CategoryType::DONATION,
                    'name' => $trans->trans('order_types.donation', [], 'messages', $locale),
                    'bidirectional' => false,
                    'disabled' => false
                ],
                [
                    'key' => CategoryType::SHREDDING,
                    'name' => $trans->trans('order_types.shredding', [], 'messages', $locale),
                    'bidirectional' => false,
                    'disabled' => false
                ],
                [
                    'key' => CategoryType::BUSYBEE,
                    'name' => $trans->trans('order_types.busybee', [], 'messages', $locale),
                    'bidirectional' => true,
                    'disabled' => false
                ],
                [
                    'key' => CategoryType::MOVING,
                    'name' => $trans->trans('order_types.moving', [], 'messages', $locale),
                    'bidirectional' => false,
                    'disabled' => false
                ],
            ];

            return new JsonResponse([
                'count' => count($items),
                'items' => $items,
            ]);

        } catch (\Exception $e) {

            return new JsonResponse([
                'message' => $e->getMessage()
            ], $e->getCode() > 300 ? $e->getCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
