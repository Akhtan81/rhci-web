<?php

namespace App\Security;

use App\Service\UserService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    /** @var ContainerInterface */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $userService = $this->container->get(UserService::class);
        $em = $this->container->get('doctrine')->getManager();

        $user = $userService->getUser();

        $user->refreshToken();

        $em->persist($user);
        $em->flush();

        $content = $userService->serialize($user);

        return new JsonResponse([
            'token' => $user->getAccessToken(),
            'user' => $content
        ]);
    }
}
