<?php

namespace App\Tests\Classes;

use App\Entity\CategoryType;
use App\Entity\PartnerStatus;
use App\Service\PartnerService;
use Symfony\Component\DependencyInjection\ContainerInterface;

trait PartnerCreator
{

    public function createPartner(ContainerInterface $container, $type = null)
    {
        $partnerService = $container->get(PartnerService::class);

        if (!$type) {
            $types = [
                CategoryType::JUNK_REMOVAL,
                CategoryType::DONATION,
                CategoryType::SHREDDING,
                CategoryType::RECYCLING
            ];

            $type = $types[array_rand($types)];
        }

        $partner = $partnerService->create([
            'accountId' => md5(uniqid()),
            'status' => PartnerStatus::APPROVED,
            'postalCodes' => [
                [
                    'postalCode' => mt_rand(10000, 99999),
                    'type' => $type
                ],
            ],
            'user' => [
                'name' => md5(uniqid()),
                'email' => md5(uniqid()) . '@mail.com',
                'password' => '12345',
            ],
            'location' => [
                'lat' => 9.9999,
                'lng' => 1.1111,
                'address' => md5(uniqid()),
                'postalCode' => '00001'
            ]

        ]);

        return $partner;
    }
}