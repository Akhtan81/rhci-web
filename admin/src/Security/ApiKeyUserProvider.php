<?php

namespace App\Security;

use App\Entity\User;
use App\Service\UserService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ApiKeyUserProvider implements UserProviderInterface
{
    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function loadUserByUsername($apiKey)
    {
        if (!$apiKey) {
            throw new BadCredentialsException('Bad credentials.');
        }

        $userService = $this->container->get(UserService::class);

        /** @var User $user */
        $user = $userService->findOneByFilter([
            'accessToken' => $apiKey
        ]);

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        throw new UnsupportedUserException();
    }

    public function supportsClass($class)
    {
        return User::class === $class;
    }
}