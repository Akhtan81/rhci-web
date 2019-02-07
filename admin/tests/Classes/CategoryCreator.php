<?php

namespace App\Tests\Classes;

use App\Entity\CategoryType;
use App\Service\CategoryService;
use Symfony\Component\DependencyInjection\ContainerInterface;

trait CategoryCreator
{

    /**
     * @param ContainerInterface $container
     * @param null $type
     * @return \App\Entity\Category
     * @throws \Exception
     */
    public function createCategory(ContainerInterface $container, $type = null)
    {
        $categoryService = $container->get(CategoryService::class);

        if (!$type) {
            $types = [
                CategoryType::JUNK_REMOVAL,
                CategoryType::DONATION,
                CategoryType::SHREDDING,
                CategoryType::RECYCLING
            ];

            $type = $types[array_rand($types)];
        }

        $category = $categoryService->create([
            'translations' => [
                [
                    'locale' => 'en',
                    'name' => md5(uniqid()),
                ],
                [
                    'locale' => 'ru',
                    'name' => md5(uniqid()),
                ]
            ],
            'type' => $type,
        ]);

        return $category;
    }
}