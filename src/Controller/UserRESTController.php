<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UserRESTController extends Controller
{

    public function putAction(Request $request, $id)
    {
        $user = $this->get(UserService::class)->getUser();

        if (!$user->isAdmin() && $user->getPartner()) {
            if (intval($id) !== $user->getId()) {

                return new JsonResponse([
                    'message' => 'Forbidden'
                ], JsonResponse::HTTP_FORBIDDEN);
            }
        }

        $contentType = $request->headers->get('Content-Type');

        switch ($contentType) {
            case 'application/json':
                $content = json_decode($request->getContent(), true);
                $uploadedFile = null;
                break;
            default:
                $content = json_decode($request->get('content'), true);
                $uploadedFile = $request->files->get('image');
        }

        if (!$content) {
            return new JsonResponse([
                'message' => 'Missing content'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $em = $this->get('doctrine')->getManager();

        $user = $em->getRepository(User::class)->find($id);
        if (!$user) {
            return new JsonResponse([
                'message' => 'Student was not found'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $service = $this->get(UserService::class);

        $em->beginTransaction();
        try {

            $service->update($user, $content, $uploadedFile);

            $em->commit();

            $item = $service->serialize($user);

            return new JsonResponse($item);

        } catch (\Exception $e) {

            $em->rollback();

            return new JsonResponse([
                'message' => $e->getMessage()
            ], $e->getCode() > 300 ? $e->getCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}