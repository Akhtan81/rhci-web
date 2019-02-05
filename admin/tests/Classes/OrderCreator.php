<?php

namespace App\Tests\Classes;

use App\Entity\CategoryType;
use App\Entity\OrderRepeat;
use App\Entity\Partner;
use App\Entity\User;
use App\Service\MediaService;
use App\Service\OrderService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait OrderCreator
{

    use PartnerCreator;
    use PartnerCategoryCreator;

    /**
     * @param ContainerInterface $container
     * @param Partner $partner
     * @param User|null $creator
     * @return \App\Entity\Order
     * @throws \Exception
     */
    public function createOrder(ContainerInterface $container, Partner $partner, User $creator = null)
    {
        $orderService = $container->get(OrderService::class);
        $mediaService = $container->get(MediaService::class);
        $root = $container->getParameter('kernel.root_dir') . '/../public';

        $partnerCategory1 = $this->createPartnerCategory($container, $partner, CategoryType::JUNK_REMOVAL);
        $partnerCategory2 = $this->createPartnerCategory($container, $partner, CategoryType::JUNK_REMOVAL);

        $path1 = $root . '/img/favicon/apple-touch-icon-114x114.png';

        copy($path1, '/tmp/apple-touch-icon-114x114.png');

        $file = new UploadedFile('/tmp/apple-touch-icon-114x114.png', 'apple-touch-icon-114x114.png', 'image/png', UPLOAD_ERR_OK, true);

        $media = $mediaService->create($file);

        $repeatables = [null, OrderRepeat::WEEK, OrderRepeat::MONTH, OrderRepeat::MONTH_3];
        $repeat = $repeatables[array_rand($repeatables)];

        $postalCode = $partner->getPostalCodes()->get(0)->getPostalCode();

        $category1 = $partnerCategory1->getId();

        $category2 = $partnerCategory2->getId();

        $content = [
            'user' => $creator ? $creator->getId() : null,
            'location' => [
                'lat' => 12.12345,
                'lng' => 21.12345,
                'address' => md5(uniqid()),
                'postalCode' => $postalCode,
            ],
            'partner' => $partner->getId(),
            'scheduledAt' => date('Y-m-d 23:59:00'),
            'repeatable' => $repeat,
            'items' => [
                [
                    'category' => $category1,
                    'quantity' => 10
                ],
                [
                    'category' => $category2,
                    'quantity' => 10
                ]
            ],
            'message' => [
                'text' => md5(uniqid()),
                'files' => [
                    $media->getId()
                ]
            ]
        ];

        $order = $orderService->create($content);


        return $order;
    }
}