<?php

namespace App\Tests\Classes;

use App\Service\CountryService;
use Symfony\Component\DependencyInjection\ContainerInterface;

trait CountryCreator
{

    /**
     * @param ContainerInterface $container
     * @return \App\Entity\Country
     * @throws \Exception
     */
    public function createCountry(ContainerInterface $container)
    {
        $service = $container->get(CountryService::class);

        $entity = $service->create([
            'currency' => 'USD',
            'translations' => [
                [
                    'locale' => 'en',
                    'name' => md5(uniqid()),
                ],
                [
                    'locale' => 'ru',
                    'name' => md5(uniqid()),
                ],
                [
                    'locale' => 'es',
                    'name' => md5(uniqid()),
                ],
                [
                    'locale' => 'kz',
                    'name' => md5(uniqid()),
                ]
            ],
        ]);

        return $entity;
    }
}