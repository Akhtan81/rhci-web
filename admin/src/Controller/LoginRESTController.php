<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class LoginRESTController extends Controller
{

    public function loginV1(Request $request)
    {
        $content = json_decode($request->getContent(), true);
        $trans = $this->get('translator');

        $username = isset($content['login']) ? $content['login'] : null;
        $password = isset($content['password']) ? $content['password'] : null;

        try {

            if (!($username && $password)) {
                throw new \Exception($trans->trans('validation.bad_request'), 400);
            }

            $em = $this->get('doctrine')->getManager();
            $encoder = $this->get('security.password_encoder');
            $userService = $this->get(UserService::class);

            /** @var User $user */
            $user = $em->getRepository(User::class)->loadUserByUsername($username);
            if (!$user) {
                throw new \Exception($trans->trans('validation.username_password_mismatch'), 401);
            }

            $isValid = $encoder->isPasswordValid($user, $password);
            if (!$isValid) {
                throw new \Exception($trans->trans('validation.username_password_mismatch'), 401);
            }

            $user->refreshToken();

            $em->persist($user);
            $em->flush();

            $content = $userService->serialize($user);

            return new JsonResponse([
                'token' => $user->getAccessToken(),
                'user' => $content
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => $e->getMessage()
            ], $e->getCode() > 300 ? $e->getCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function loginV2()
    {
        throw $this->createAccessDeniedException();
    }
}