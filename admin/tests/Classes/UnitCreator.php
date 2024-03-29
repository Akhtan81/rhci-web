<?php

namespace App\Tests\Classes;

use App\Service\UnitService;
use Symfony\Component\DependencyInjection\ContainerInterface;

trait UnitCreator
{

    /**
     * @param ContainerInterface $container
     * @return \App\Entity\Unit
     * @throws \Exception
     */
    public function createUnit(ContainerInterface $container)
    {
        $unitService = $container->get(UnitService::class);

        $unit = $unitService->create([
            'name' => md5(uniqid()),
        ]);

        return $unit;
    }
}