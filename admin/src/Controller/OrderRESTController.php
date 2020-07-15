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
        $admin = $this->get(UserService::class)->getAdmin();
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

        if (!$admin) {
            $filter['user'] = $user->getId();
        }

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
        //file_put_contents("request.txt", $request);
        $em = $this->get('doctrine')->getManager();
        try {
            $trans = $this->get('translator');
            $user = $this->get(UserService::class)->getUser();
            if (!$user)
                return new JsonResponse([
                    'message' => $trans->trans('validation.unauthorized')
                ], JsonResponse::HTTP_UNAUTHORIZED);

            //$content = file_get_contents("content.txt");
            //$content = json_decode($content, true);
            $content = json_decode($request->getContent(), true);
            $errors = "";
            $locale = $request->getLocale();
            $service = $this->get(OrderService::class);
            //set primary method in stripe api
            $cardString = $content['selected_payment_method'];
            $stripeSecretKey = $this->container->getParameter('stripe_client_secret');
            $customerId = $user->getCustomerId();
            //retrieve customer
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/customers/'.$customerId);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_USERPWD, $stripeSecretKey . ':' . '');

            $result = curl_exec($ch);
            if (curl_errno($ch))
                return new JsonResponse([
                    'message' => $trans->trans('validation.error_occured')
                ], 500);
            curl_close($ch);
            $customer = json_decode($result);
            if(!property_exists($customer,'invoice_settings') 
               || !property_exists($customer->invoice_settings,'default_payment_method'))
                return new JsonResponse([
                    'message' => $trans->trans('validation.corrupted_data')
                ], 500);
            //list payment methods
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/payment_methods?customer='.$customerId.'&type=card&limit=10');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERPWD, $stripeSecretKey . ':');
            $headers = array();
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($ch);
            if (curl_errno($ch))
                return new JsonResponse([
                    'message' => $trans->trans('validation.error_occured')
                ], 500);
            curl_close($ch);
            $pmObject = json_decode($result);
            $found = -1;
            if(!property_exists($pmObject, "data")
               || !is_array($pmObject->data))
                return new JsonResponse([
                    'message' => $trans->trans('validation.corrupted_data')
                ], 500);
            if(sizeof($pmObject->data)==1){
                $found = $pmObject->data[0]->id;
            }else{
                foreach($pmObject->data as $pm){
                    if(!is_object($pm)
                        || !property_exists($pm, "card")
                        || !property_exists($pm->card, "brand")
                        || !property_exists($pm->card, "last4")
                    ){
                        return new JsonResponse(['message' => $trans->trans('validation.corrupted_data')], 500);
                    }
                    $b = $pm->card->brand;
                    $f = $pm->card->last4;
                    if(
                        strcasecmp($cardString, $b) == 0
                        || (strpos($cardString, ' ') !== false && (
                            strcasecmp(explode(" ",$cardString)[0], $b) == 0
                            && strcasecmp(explode(" ",$cardString)[1], $f) == 0
                        ))
                    ){
                        $found = $pm->id;
                        break;
                    }
                }
            }
            if($found!==-1){
                //updating customer's default payment method
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/customers/'.$customerId);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, "invoice_settings[default_payment_method]=".$found);
                curl_setopt($ch, CURLOPT_USERPWD, $stripeSecretKey . ':' . '');
                $headers = array();
                $headers[] = 'Content-Type: application/x-www-form-urlencoded';
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                $result = curl_exec($ch);
                if(curl_errno($ch)){
                    return new JsonResponse(['message' => $trans->trans('validation.payment_method_not_saved')], 500);
                    //echo curl_error($ch);
                }
                curl_close($ch);
            }else{
                return new JsonResponse(['message' => $trans->trans('validation.card_not_found')], 500);
            }
            $em->beginTransaction();
            
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
            //file_put_contents("exception.txt", $e->getMessage());
            return new JsonResponse([
                'message' => $e->getMessage()
            ], $e->getCode() > 300 ? $e->getCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        /*return new JsonResponse([
            'message' => $trans->trans('validation.provide_selected_payment_method')
        ], 500);
        return new JsonResponse([
            'message' => "Done"
        ], 500);*/
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
