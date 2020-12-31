<?php

namespace App\Security;

use App\Entity\PartnerStatus;
use App\Entity\User;
use App\Service\UserService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class LoginUserProvider implements UserProviderInterface
{
    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function loadUserByUsername($username)
    {
        if (!$username) {
            throw new BadCredentialsException();
        }

        $userService = $this->container->get(UserService::class);

        $user = $userService->findOneByFilter([
            'isActive' => true,
            'partnerStatus' => PartnerStatus::APPROVED,
            'login' => $username
        ]);

        if ($user) {

            if ($user->isAdmin()) {
                return $user;
            }

            $partner = $user->getPartner();

            //if ($partner && $partner->getStatus() === PartnerStatus::APPROVED) {
                return $user;
            //}
        }else{
            $user2 = $userService->findOneByFilter([
                'isActive' => true,
                'login' => $username
            ]);
            return $user2;
        }

        throw new BadCredentialsException();
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