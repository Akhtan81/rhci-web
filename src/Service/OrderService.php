<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Message;
use App\Entity\MessageMedia;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\OrderRepeat;
use App\Entity\OrderStatus;
use JMS\Serializer\SerializationContext;
use Symfony\Component\DependencyInjection\ContainerInterface;

class OrderService
{

    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param $content
     *
     * @return Order
     * @throws \Exception
     */
    public function create($content)
    {
        $user = $this->container->get(UserService::class)->getUser();
        $stripe = $this->container->get(StripeService::class);

        $entity = new Order();
        $entity->setUser($user);

        if (isset($content['items'])) {
            foreach ($content['items'] as $item) {
                $this->handleOrderItem($entity, $item);
            }
        }

        if (isset($content['message'])) {
            $this->handleMessage($entity, $content['message']);
        }

        $this->update($entity, $content);

        $payment = $stripe->createPayment($entity);

        $entity->getPayments()->add($payment);

        return $entity;
    }

    /**
     * @param Order $entity
     * @param $content
     *
     * @throws \Exception
     */
    public function update(Order $entity, $content)
    {
        $em = $this->container->get('doctrine')->getManager();
        $trans = $this->container->get('translator');
        $user = $this->container->get(UserService::class)->getUser();
        $locationService = $this->container->get(LocationService::class);
        $userLocationService = $this->container->get(UserLocationService::class);
        $partnerService = $this->container->get(PartnerService::class);

        $now = new \DateTime();

        $entity->setUpdatedAt($now);
        $entity->setUpdatedBy($user);

        if (isset($content['scheduledAt'])) {
            $today = \DateTime::createFromFormat('Y-m-d H:i:s', $entity->getCreatedAt()->format('Y-m-d H:00:00'));
            $date = \DateTime::createFromFormat('Y-m-d H:i:s', $content['scheduledAt']);
            if (!$date || $date < $today) {
                throw new \Exception($trans->trans('validation.invalid_scheduled_at'), 400);
            }

            $entity->setScheduledAt($date);
        }

        if (isset($content['isScheduleApproved'])) {
            $entity->setIsScheduleApproved($content['isScheduleApproved'] === true);
        }

        if (isset($content['isPriceApproved'])) {
            $entity->setIsPriceApproved($content['isPriceApproved'] === true);
        }

        if (isset($content['repeatable'])) {
            switch ($content['repeatable']) {
                case OrderRepeat::MONTH:
                case OrderRepeat::MONTH_3:
                case OrderRepeat::WEEK:
                    $entity->setRepeatable($content['repeatable']);
                    break;
                default:
                    throw new \Exception($trans->trans('validation.bad_request'), 400);
            }
        }

        if (isset($content['status'])) {
            $this->handleStatusChange($entity, $content['status']);
        }

        if (isset($content['location'])) {
            $location = $locationService->create($content['location'], false);

            $entity->setLocation($location);

            $orderCreator = $entity->getUser();

            $userLocation = $userLocationService->create($orderCreator, $location, false);

            $orderCreator->setLocation($userLocation);

            if (!$orderCreator->getLocations()->contains($userLocation)) {
                $orderCreator->getLocations()->add($userLocation);
            }

            $em->persist($orderCreator);
        }

        if (!$entity->getLocation()) {
            throw new \Exception($trans->trans('validation.order_location_not_found'), 404);
        }

        $partner = $partnerService->findOneByFilter([
            'postalCode' => $entity->getLocation()->getPostalCode()
        ]);
        if (!$partner) {
            throw new \Exception($trans->trans('validation.partner_not_found_by_postal_code'), 404);
        }

        $entity->setPartner($partner);

        switch ($entity->getStatus()) {
            case OrderStatus::CREATED:
            case OrderStatus::APPROVED:
            case OrderStatus::IN_PROGRESS:

                if (isset($content['price'])) {
                    $entity->setPrice($content['price']);
                } else {
                    $this->handleOrderPrice($entity);
                }

                break;
        }

        $em->persist($entity);
        $em->flush();
    }

    private function handleOrderPrice(Order $entity)
    {
        $em = $this->container->get('doctrine')->getManager();
        $trans = $this->container->get('translator');
        $partnerCategoryService = $this->container->get(PartnerCategoryService::class);

        $totalPrice = 0;

        /** @var OrderItem $item */
        foreach ($entity->getItems() as $item) {

            $partnerCategory = $partnerCategoryService->findOneByFilter([
                'category' => $item->getCategory()->getId(),
                'partner' => $entity->getPartner()->getId(),
            ]);
            if (!$partnerCategory) {
                throw new \Exception($trans->trans('validation.not_found'), 404);
            }

            $item->setPartnerCategory($partnerCategory);

            if ($partnerCategory->getCategory()->hasPrice()) {
                $item->setPrice($partnerCategory->getPrice());

                $totalPrice += $item->getPrice() * $item->getQuantity();
            }

            $em->persist($item);
        }

        $entity->setPrice($totalPrice);
    }

    private function handleStatusChange(Order $entity, $status)
    {
        if ($entity->getStatus() === $status) return;

        $trans = $this->container->get('translator');

        switch ($entity->getStatus()) {
            case OrderStatus::CREATED:

                switch ($status) {
                    case OrderStatus::REJECTED:
                    case OrderStatus::APPROVED:
                    case OrderStatus::CANCELED:
                        $entity->setStatus($status);
                        break;
                    default:
                        throw new \Exception($trans->trans('validation.forbidden_order_status'), 400);
                }

                break;
            case OrderStatus::APPROVED:

                switch ($status) {
                    case OrderStatus::CANCELED:
                    case OrderStatus::IN_PROGRESS:
                        $entity->setStatus($status);
                        break;
                    default:
                        throw new \Exception($trans->trans('validation.forbidden_order_status'), 400);
                }

                break;
            case OrderStatus::IN_PROGRESS:

                switch ($status) {
                    case OrderStatus::CANCELED:
                    case OrderStatus::DONE:
                        $entity->setStatus($status);
                        break;
                    default:
                        throw new \Exception($trans->trans('validation.forbidden_order_status'), 400);
                }

                break;
            default:
                throw new \Exception($trans->trans('validation.forbidden_order_status'), 400);
        }
    }

    /**
     * @param Order $entity
     * @param $content
     *
     * @return OrderItem
     * @throws \Exception
     */
    private function handleOrderItem(Order $entity, $content)
    {
        $em = $this->container->get('doctrine')->getManager();
        $trans = $this->container->get('translator');


        /** @var Category $category */
        $category = $em->getRepository(Category::class)->find($content['category']);
        if (!$category) {
            throw new \Exception($trans->trans('validation.not_found'), 404);
        }

        $item = new OrderItem();
        $item->setOrder($entity);
        $item->setCategory($category);
        $item->setQuantity($content['quantity']);

        $em->persist($item);

        $entity->addItem($item);
        $entity->setType($category->getType());

        return $item;
    }

    /**
     * @param Order $entity
     * @param $content
     *
     * @throws \Exception
     */
    private function handleMessage(Order $entity, $content)
    {
        $em = $this->container->get('doctrine')->getManager();
        $trans = $this->container->get('translator');
        $user = $this->container->get(UserService::class)->getUser();
        $mediaService = $this->container->get(MediaService::class);

        $message = new Message();
        $message->setUser($user);
        $message->setOrder($entity);
        $message->setText($content['text']);

        if (isset($content['files'])) {

            $ids = $content['files'];

            $medias = $mediaService->findByFilter([
                'ids' => $ids
            ]);
            if (count($medias) !== count($ids)) {
                throw new \Exception($trans->trans('validation.not_found'), 404);
            }

            foreach ($medias as $media) {
                $messageMedia = new MessageMedia();
                $messageMedia->setMedia($media);
                $messageMedia->setMessage($message);

                $em->persist($messageMedia);

                $message->addMedia($messageMedia);
            }
        }

        $entity->addMessage($message);

        $em->persist($message);
    }

    /**
     * @param array $filter
     *
     * @return int
     * @throws \Exception
     */
    public function countByFilter(array $filter = [])
    {
        $em = $this->container->get('doctrine')->getManager();

        return $em->getRepository(Order::class)->countByFilter($filter);
    }

    /**
     * @param array $filter
     * @param int $page
     * @param int $limit
     *
     * @return array
     */
    public function findByFilter(array $filter = [], $page = 0, $limit = 0)
    {
        $em = $this->container->get('doctrine')->getManager();

        return $em->getRepository(Order::class)->findByFilter($filter, $page, $limit);
    }

    /**
     * @param array $filter
     *
     * @return Order|null
     */
    public function findOneByFilter(array $filter = [])
    {
        $items = $this->findByFilter($filter, 1, 1);
        if (count($items) !== 1) return null;

        return $items[0];
    }

    public function serialize($content)
    {
        return json_decode($this->container->get('jms_serializer')
            ->serialize($content, 'json', SerializationContext::create()
                ->setGroups(['api_v1'])), true);
    }

    public function serializeV2($content)
    {
        return json_decode($this->container->get('jms_serializer')
            ->serialize($content, 'json', SerializationContext::create()
                ->setGroups(['api_v1', 'api_v2'])), true);
    }


}