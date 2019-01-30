<?php

namespace App\Service;

use App\Entity\CategoryType;
use App\Entity\ItemMessage;
use App\Entity\ItemMessageMedia;
use App\Entity\Location;
use App\Entity\Message;
use App\Entity\MessageMedia;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\OrderRepeat;
use App\Entity\OrderStatus;
use App\Entity\PartnerCategory;
use App\Entity\PartnerStatus;
use App\Entity\Payment;
use App\Entity\PaymentStatus;
use App\Entity\PaymentType;
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
     * @param null $id
     * @return array
     */
    public function getPartnerAccessFilter($id = null)
    {
        $userService = $this->container->get(UserService::class);

        $partner = $userService->getPartner();

        $filter = [];

        if ($id) {
            $filter['id'] = $id;
        }

        if ($partner) {
            $filter['partner'] = $partner->getId();
        }

        return $filter;
    }

    /**
     * @param $content
     *
     * @return Order
     * @throws \Exception
     */
    public function create($content)
    {
        $minimalPaymentAmount = intval($this->container->getParameter('minimal_payment_amount'));

        $trans = $this->container->get('translator');
        $em = $this->container->get('doctrine')->getManager();
        $stripe = $this->container->get(PaymentService::class);
        $userService = $this->container->get(UserService::class);

        $canEditSensitiveInfo = $this->canEditSensitiveInfo();

        $now = new \DateTime();
        $entity = new Order();

        if ($canEditSensitiveInfo && isset($content['user'])) {
            $user = $userService->findOneByFilter([
                'id' => $content['user']
            ]);
            if (!$user) {
                throw new \Exception($trans->trans('validation.not_found'), 404);
            }

        } else {
            $user = $userService->getUser();
        }

        $entity->setUser($user);
        $entity->setUpdatedAt($now);
        $entity->setUpdatedBy($user);

        if (isset($content['items'])) {
            foreach ($content['items'] as $item) {
                $this->handleOrderItem($entity, $item);
            }
        }

        if (isset($content['message'])) {
            $this->handleMessage($entity, $content['message']);
        }

        if (isset($content['location'])) {
            $this->handleLocation($entity, $content['location']);
        }

        if (isset($content['partner'])) {
            $this->handlePartner($entity, $content['partner']);
        }

        $this->update($entity, $content);

        $location = $entity->getLocation();
        $partner = $entity->getPartner();

        if (!$location || !$location->getPostalCode()) {
            $this->failOrderCreation($entity, $trans->trans('validation.order_location_not_found'));
        }

        if (!$partner) {
            $this->failOrderCreation($entity, $trans->trans('validation.partner_not_found'));
        }

        switch ($entity->getStatus()) {
            case OrderStatus::CREATED:

                $price = max($minimalPaymentAmount, $entity->getPrice());

                $payment = $stripe->createPayment($entity, $price);
                if ($payment) {
                    $entity->getPayments()->add($payment);
                }

                break;
        }

        $em->flush();

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
        $pushService = $this->container->get(PushService::class);

        $canEditSensitiveInfo = $this->canEditSensitiveInfo();
        $isOrderCanceled = false;
        $isOrderInProgress = false;

        $now = new \DateTime();

        $entity->setUpdatedAt($now);
        $entity->setUpdatedBy($user);

        if ($canEditSensitiveInfo) {

            if (isset($content['isScheduleApproved'])) {
                $entity->setIsScheduleApproved($content['isScheduleApproved'] === true);
            }

            if (isset($content['isPriceApproved'])) {
                $entity->setIsPriceApproved($content['isPriceApproved'] === true);
            }

        }

        if (isset($content['scheduledAt'])) {
            $today = \DateTime::createFromFormat('Y-m-d H:i:s', $entity->getCreatedAt()->format('Y-m-d H:00:00'));

            $date = \DateTime::createFromFormat('Y-m-d H:i:s', $content['scheduledAt']);
            if (!$date) {
                $date = \DateTime::createFromFormat('Y-m-d H:i', $content['scheduledAt']);
            }

            if (!$date || $date < $today) {
                throw new \Exception($trans->trans('validation.invalid_scheduled_at'), 400);
            }

            $entity->setScheduledAt($date);
        }

        if (isset($content['status'])) {
            $isOrderCanceled = $content['status'] === OrderStatus::CANCELED
                && $entity->getStatus() !== OrderStatus::CANCELED;

            $isOrderInProgress = $content['status'] === OrderStatus::IN_PROGRESS
                && $entity->getStatus() !== OrderStatus::IN_PROGRESS;

            $this->handleStatusChange($entity, $content['status']);
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

        switch ($entity->getStatus()) {
            case OrderStatus::FAILED:
            case OrderStatus::REJECTED:
                break;
            case OrderStatus::CREATED:
            case OrderStatus::APPROVED:
            case OrderStatus::IN_PROGRESS:

                if ($entity->getId() && isset($content['price'])) {
                    $this->handlePriceChanged($entity, $content['price']);
                } else {
                    $this->handleOrderPrice($entity);
                }

                break;
            default:
                if ($isOrderCanceled) {
                    $this->makeFullRefund($entity);
                }
        }

        $em->persist($entity);
        $em->flush();

        if ($isOrderInProgress) {
            $user = $entity->getUser();
            $pushService->sendPickupInProgress($user->getId());
        }
    }

    private function failOrderCreation(Order $entity, $reason)
    {
        $em = $this->container->get('doctrine')->getManager();

        $entity->setStatus(OrderStatus::FAILED);
        $entity->setStatusReason($reason);
        $entity->setDeletedAt(new \DateTime());

        $em->persist($entity);
//        $em->flush();
    }

    private function handleLocation(Order $entity, $content)
    {
        $locationService = $this->container->get(LocationService::class);
        $userLocationService = $this->container->get(UserLocationService::class);

        $location = $entity->getLocation();
        if (!$location) {
            $location = new Location();
        }

        $locationService->update($location, $content, false);

        $entity->setLocation($location);

        $orderCreator = $entity->getUser();

        $userLocationService->create($orderCreator, $location, false);
    }

    private function handlePriceChanged(Order $entity, $newPrice)
    {
        $paymentService = $this->container->get(PaymentService::class);

        $oldPrice = $entity->getPrice();

        $delta = abs($newPrice - $oldPrice);

        if ($delta > 0) {
            $payment = null;

            if ($newPrice > $oldPrice) {
                $payment = $paymentService->createPayment($entity, $delta);
            } else {
                $lastPayment = $paymentService->findOneByFilter([
                    'type' => PaymentType::PAYMENT,
                    'status' => PaymentStatus::SUCCESS,
                    'order' => $entity->getId()
                ]);
                if ($lastPayment) {
                    $payment = $paymentService->createRefund($lastPayment, $delta);
                }
            }

            if ($payment) {
                $entity->getPayments()->add($payment);
            }
        }

        $entity->setPrice($newPrice);
    }

    private function makeFullRefund(Order $entity)
    {
        $paymentService = $this->container->get(PaymentService::class);

        $price = $entity->getPrice();
        if ($price > 0) {
            $payments = $paymentService->findByFilter([
                'order' => $entity->getId()
            ]);

            /** @var Payment $payment */
            foreach ($payments as $payment) {
                if ($payment->isRefunded()) continue;

                $refund = $paymentService->createRefund($payment, $price, false);

                $entity->getPayments()->add($refund);
            }
        }
    }

    private function handleOrderPrice(Order $entity)
    {
        $em = $this->container->get('doctrine')->getManager();

        $totalPrice = 0;

        /** @var OrderItem $item */
        foreach ($entity->getItems() as $item) {

            $partnerCategory = $item->getPartnerCategory();

            if ($partnerCategory->getPrice() >= 0) {

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
        $user = $this->container->get(UserService::class)->getUser();
        $partnerCategoryService = $this->container->get(PartnerCategoryService::class);

        /** @var PartnerCategory $partnerCategory */
        $partnerCategory = $partnerCategoryService->findOneByFilter([
            'id' => $content['category']
        ]);
        if (!$partnerCategory) {
            throw new \Exception($trans->trans('validation.not_found'), 404);
        }

        $category = $partnerCategory->getCategory();

        $item = new OrderItem();
        $item->setOrder($entity);
        $item->setCategory($category);
        $item->setPartnerCategory($partnerCategory);
        $item->setQuantity($content['quantity']);

        $em->persist($item);

        $entity->addItem($item);
        $entity->setType($category->getType());

        $mediaService = $this->container->get(MediaService::class);

        if (isset($content['message'])) {
            $msgContent = $content['message'];

            $message = new ItemMessage();
            $message->setUser($user);
            $message->setItem($item);

            if (isset($msgContent['text'])) {
                $message->setText($msgContent['text']);
            }

            if (isset($msgContent['files']) && $msgContent['files']) {

                $ids = $msgContent['files'];

                $medias = $mediaService->findByFilter([
                    'ids' => $ids
                ]);
                if (count($medias) !== count($ids)) {
                    throw new \Exception($trans->trans('validation.not_found'), 404);
                }

                foreach ($medias as $media) {
                    $messageMedia = new ItemMessageMedia();
                    $messageMedia->setMedia($media);
                    $messageMedia->setMessage($message);

                    $em->persist($messageMedia);

                    $message->addMedia($messageMedia);
                }
            }

            $item->setMessage($message);

            $em->persist($message);
        }

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

        if (isset($content['text'])) {
            $message->setText($content['text']);
        }

        if (isset($content['files']) && $content['files']) {

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

    private function handlePartner(Order $entity, $id)
    {
        $trans = $this->container->get('translator');
        $partnerService = $this->container->get(PartnerService::class);

        $partner = $partnerService->findOneByFilter([
            'id' => $id,
            'status' => PartnerStatus::APPROVED,
        ]);

        if (!$partner) {
            $this->failOrderCreation($entity, $trans->trans('validation.partner_not_found'));
            return;
        }

        switch ($entity->getType()) {
            case CategoryType::SHREDDING:
                if (!$partner->canManageShreddingOrders()) {
                    $this->failOrderCreation($entity, $trans->trans('validation.partner_cannot_manage_order'));
                }
                break;
            case CategoryType::JUNK_REMOVAL:
                if (!$partner->canManageJunkRemovalOrders()) {
                    $this->failOrderCreation($entity, $trans->trans('validation.partner_cannot_manage_order'));
                }
                break;
            case CategoryType::DONATION:
                if (!$partner->canManageDonationOrders()) {
                    $this->failOrderCreation($entity, $trans->trans('validation.partner_cannot_manage_order'));
                }
                break;
            case CategoryType::RECYCLING:

//                $subscription = $subscriptionService->findOneByFilter([
//                    'partner' => $partner->getId(),
//                    'status' => SubscriptionStatus::ACTIVE
//                ]);
//
//                if (!$subscription) {
//                    $this->failOrderCreation($entity, $trans->trans('validation.partner_not_found'));
//                    return;
//                }

                if (!$partner->canManageRecyclingOrders()) {
                    $this->failOrderCreation($entity, $trans->trans('validation.partner_cannot_manage_order'));
                }
                break;
        }

        $entity->setPartner($partner);
    }

    private function canEditSensitiveInfo()
    {
        $userService = $this->container->get(UserService::class);
        $admin = $userService->getAdmin();
        $partner = $userService->getPartner();

        return $admin || $partner;
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

    /**
     * @param $content
     * @param array $groups
     *
     * @return array
     */
    public function serialize($content, $groups = [])
    {
        $result = json_decode($this->container->get('jms_serializer')
            ->serialize($content, 'json', SerializationContext::create()
                ->setGroups(array_merge(['api_v1'], $groups))), true);

        if ($content instanceof Order) {
            $this->onPostSerialize($result);
        } else {
            foreach ($result as &$item) {
                $this->onPostSerialize($item);
            }
        }

        return $result;
    }

    /**
     * @param $content
     *
     * @return array
     */
    public function serializeV2($content)
    {
        return $this->serialize($content, ['api_v2']);
    }

    private function onPostSerialize(&$content)
    {
        $trans = $this->container->get('translator');

        if (isset($content['messages'][0])) {
            $content['message'] = $content['messages'][0];
        }

        unset($content['messages']);

        $locale = null;
        if (isset($content['items']) && count($content['items']) > 0) {

            $item = $content['items'][0];

            if (isset($item['category']) && isset($item['category']['locale'])) {
                $locale = $item['category']['locale'];
            }
        }

        if (isset($content['type'])) {
            $content['type'] = [
                'key' => $content['type'],
                'name' => $trans->trans('order_types.' . $content['type'], [], 'messages', $locale),
            ];
        }
    }


}
