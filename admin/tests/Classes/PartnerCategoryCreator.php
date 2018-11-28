<?php

namespace App\Tests\Classes;

use App\Entity\Partner;
use App\Service\PartnerCategoryService;
use App\Service\UnitService;
use Symfony\Component\DependencyInjection\ContainerInterface;

trait PartnerCategoryCreator
{
    use CategoryCreator;

    /**
     * @param ContainerInterface $container
     * @param Partner $partner
     * @param null $type
     * @return \App\Entity\PartnerCategory
     * @throws \Exception
     */
    public function createPartnerCategory(ContainerInterface $container, Partner $partner, $type = null)
    {
        $unitService = $container->get(UnitService::class);
        $service = $container->get(PartnerCategoryService::class);

        $category = $this->createCategory($container, $type);

        $unit = $unitService->create([
            'name' => md5(uniqid()),
        ]);

        $partnerCategory = $service->create($partner, $category, [
            'unit' => $unit->getId(),
            'minAmount' => rand(10, 1000),
            'price' => rand(10, 1000)
        ]);

        return $partnerCategory;
    }
}