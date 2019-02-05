<?php

namespace App\Tests\Classes;

use App\Service\UserService;
use Symfony\Component\DependencyInjection\ContainerInterface;

trait UserCreator
{

    /**
     * @param ContainerInterface $container
     * @return \App\Entity\User
     * @throws \Exception
     */
    public function createUser(ContainerInterface $container)
    {
        $service = $container->get(UserService::class);

        $user = $service->create([
            'name' => md5(uniqid()),
            'email' => md5(uniqid()) . '@mail.com',
            'password' => '12345',
        ]);

        return $user;
    }
}