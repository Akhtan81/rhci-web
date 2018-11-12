<?php

namespace App\Controller;

use App\Service\StripeWebhookService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class StripeWebhookRESTController extends Controller
{

    public function post(Request $request)
    {
        $service = $this->get(StripeWebhookService::class);

        $content = json_decode($request->getContent(), true);

        try {

            $service->handleEvent($content);

            return new JsonResponse([
                'status' => 'ok',
            ], JsonResponse::HTTP_OK);

        } catch (\Exception $e) {

            return new JsonResponse([
                'message' => $e->getMessage()
            ], $e->getCode() > 300 ? $e->getCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}