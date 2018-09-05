<?php

namespace App\Controller;

use App\Entity\Role;
use App\Service\MediaService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class MediaRESTController extends Controller
{
    public function postAction(Request $request)
    {
        $this->denyAccessUnlessGranted(Role::USER);

        $trans = $this->get('translator');
        $file = $request->files->get('file');

        if (!$file) {
            return new JsonResponse([
                'message' => $trans->trans('validation.bad_request')
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $service = $this->get(MediaService::class);

        try {

            $entity = $service->create($file);

            $item = $service->serialize($entity);

            return new JsonResponse($item, JsonResponse::HTTP_CREATED);

        } catch (\Exception $e) {

            return new JsonResponse([
                'message' => $e->getMessage()
            ], $e->getCode() > 300 ? $e->getCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}