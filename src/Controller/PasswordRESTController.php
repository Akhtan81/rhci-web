<?php

namespace App\Controller;

use App\Service\EmailService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PasswordRESTController extends Controller
{
    public function postResetAction(Request $request)
    {
        $trans = $this->get('translator');
        $service = $this->get(UserService::class);
        $email = $this->get(EmailService::class);

        $content = json_decode($request->getContent(), true);

        if (!isset($content['login'])) {
            return new JsonResponse([
                'message' => $trans->trans('validation.bad_request')
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $em = $this->get('doctrine')->getManager();

        $user = $service->findOneByFilter([
            'login' => $content['login']
        ]);
        if (!$user) {
            return new JsonResponse([
                'message' => $trans->trans('validation.not_found')
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        try {

            $user->refreshPasswordToken();

            $em->persist($user);
            $em->flush();

            if ($user->getPartner()) {
                $email->sentResetPassword($user);
            }

            $item = $service->serialize($user);

            return new JsonResponse($item);

        } catch (\Exception $e) {

            return new JsonResponse([
                'message' => $e->getMessage()
            ], $e->getCode() > 300 ? $e->getCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function putSetAction(Request $request, $token)
    {
        $trans = $this->get('translator');
        $service = $this->get(UserService::class);

        $user = $service->findOneByFilter([
            'passwordToken' => $token,
            'isPasswordTokenExpired' => false
        ]);
        if (!$user) {
            return new JsonResponse([
                'message' => $trans->trans('validation.not_found')
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $content = json_decode($request->getContent(), true);

        try {

            $user->setPasswordToken(null);
            $user->setPasswordTokenExpiresAt(null);

            $service->update($user, $content);

            $item = $service->serialize($user);

            return new JsonResponse($item);

        } catch (\Exception $e) {

            return new JsonResponse([
                'message' => $e->getMessage()
            ], $e->getCode() > 300 ? $e->getCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}