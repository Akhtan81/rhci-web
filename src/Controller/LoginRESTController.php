<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class LoginRESTController extends Controller
{


    public function login(Request $request)
    {
        $content = json_decode($request->getContent(), true);

        $credentials = $content['security']['credentials'];

        $username = isset($credentials['login']) ? $credentials['login'] : null;
        $password = isset($credentials['password']) ? $credentials['password'] : null;

        try {
            $em = $this->get('doctrine')->getManager();
            $encoder = $this->get('security.password_encoder');
            $userService = $this->get(UserService::class);

            /** @var User $user */
            $user = $em->getRepository(User::class)->loadUserByUsername($username);
            if (!$user) {
                throw new \Exception('Bad credentials.', 401);
            }

            $isValid = $encoder->isPasswordValid($user, $password);
            if (!$isValid) {
                throw new \Exception('Bad credentials.', 401);
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
}