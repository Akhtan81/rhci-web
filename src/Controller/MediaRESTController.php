<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class MediaRESTController extends Controller
{
    public function postAction(Request $request)
    {
        $file = $request->files->get('file');

        if (!$file) {
            return new JsonResponse([
                'message' => 'Missing file'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $em = $this->get('doctrine')->getManager();

        $service = $this->get(MediaService::class);

        $em->beginTransaction();
        try {

            $entity = $service->create($file);

            $em->commit();

            $item = $service->serialize($entity);

            return new JsonResponse($item, JsonResponse::HTTP_CREATED);

        } catch (\Exception $e) {

            $em->rollback();

            return new JsonResponse([
                'message' => $e->getMessage()
            ], $e->getCode() > 300 ? $e->getCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}