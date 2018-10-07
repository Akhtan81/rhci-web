<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\ItemMessage;
use App\Entity\ItemMessageMedia;
use App\Entity\Location;
use App\Entity\Message;
use App\Entity\MessageMedia;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\OrderRepeat;
use App\Entity\OrderStatus;
use App\Entity\PartnerStatus;
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
     * @param $content
     *
     * @return Order
     * @throws \Exception
     */
    public function create($content)
    {
        $minimalPaymentAmount = intval($this->container->getParameter('minimal_payment_amount'));
        $trans = $this->container->get('translator');

        $stripe = $this->container->get(PaymentService::class);
        $userService = $this->container->get(UserService::class);

        $canEditSensitiveInfo = $this->canEditSensitiveInfo();

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

        if (isset($content['items'])) {
            foreach ($content['items'] as $item) {
                $this->handleOrderItem($entity, $item);
            }
        }

        if (isset($content['message'])) {
            $this->handleMessage($entity, $content['message']);
        }

        $this->update($entity, $content);

        switch ($entity->getStatus()) {
            case OrderStatus::CREATED:

                $price = max($minimalPaymentAmount, $entity->getPrice());

                $payment = $stripe->createPayment($entity, $price);

                $entity->getPayments()->add($payment);

                break;
        }

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

        $canEditSensitiveInfo = $this->canEditSensitiveInfo();
        $isOrderCanceled = false;

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

        if ($canEditSensitiveInfo && isset($content['isScheduleApproved'])) {
            $entity->setIsScheduleApproved($content['isScheduleApproved'] === true);
        }

        if ($canEditSensitiveInfo && isset($content['isPriceApproved'])) {
            $entity->setIsPriceApproved($content['isPriceApproved'] === true);
        }

        if (isset($content['status'])) {
            $isOrderCanceled = $content['status'] === OrderStatus::CANCELED
                && $entity->getStatus() !== OrderStatus::CANCELED;

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

        if (isset($content['location'])) {
            $location = $entity->getLocation();
            if (!$location) {
                $location = new Location();
            }

            $locationService->update($location, $content['location'], false);

            $entity->setLocation($location);

            $orderCreator = $entity->getUser();

            $userLocationService->create($orderCreator, $location, false);
        }

        $location = $entity->getLocation();

        if (!$location || !$location->getPostalCode()) {
            $this->failOrderCreation($entity, $trans->trans('validation.order_location_not_found'));
            return;
        }

        if (!$entity->getPartner()) {
            $partner = $partnerService->findOneByFilter([
                'postalCode' => $location->getPostalCode(),
                'status' => PartnerStatus::APPROVED,
                'type' => $entity->getType()
            ]);
            if (!$partner) {
                $this->failOrderCreation($entity, $trans->trans('validation.partner_not_found_by_postal_code'));
                return;
            }

            $entity->setPartner($partner);
        }

        switch ($entity->getStatus()) {
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
    }

    private function failOrderCreation(Order $entity, $reason)
    {
        $em = $this->container->get('doctrine')->getManager();

        $entity->setStatus(OrderStatus::FAILED);
        $entity->setStatusReason($reason);
        $entity->setDeletedAt(new \DateTime());

        $em->persist($entity);
        $em->flush();
    }

    private function handlePriceChanged(Order $entity, $newPrice)
    {
        $paymentService = $this->container->get(PaymentService::class);
        $trans = $this->container->get('translator');

        $oldPrice = $entity->getPrice();

        $delta = abs($newPrice - $oldPrice);

        if ($delta > 0) {
            if ($newPrice > $oldPrice) {
                $payment = $paymentService->createPayment($entity, $delta);
            } else {
                $lastPayment = $paymentService->findOneByFilter([
                    'type' => PaymentType::PAYMENT,
                    'status' => PaymentStatus::SUCCESS,
                    'order' => $entity->getId()
                ]);
                if (!$lastPayment) {
                    throw new \Exception($trans->trans('validation.invalid_refund'), 404);
                }

                $payment = $paymentService->createRefund($lastPayment, $delta);
            }

            $entity->getPayments()->add($payment);
        }

        $entity->setPrice($newPrice);
    }

    private function makeFullRefund(Order $entity)
    {
        $paymentService = $this->container->get(PaymentService::class);
        $trans = $this->container->get('translator');

        $price = $entity->getPrice();
        if ($price > 0) {
            $payments = $paymentService->findByFilter([
                'order' => $entity->getId()
            ]);
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
        $trans = $this->container->get('translator');
        $partnerCategoryService = $this->container->get(PartnerCategoryService::class);

        $totalPrice = 0;

        /** @var OrderItem $item */
        foreach ($entity->getItems() as $item) {

            $partnerCategory = $partnerCategoryService->findOneByFilter([
                'partnerStatus' => PartnerStatus::APPROVED,
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
        $user = $this->container->get(UserService::class)->getUser();


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

        $mediaService = $this->container->get(MediaService::class);

        if (isset($content['message'])) {
            $msgContent = $content['message'];

            $message = new ItemMessage();
            $message->setUser($user);
            $message->setItem($item);
            $message->setText($msgContent['text']);

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
        $message->setText($content['text']);

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
        if (isset($content['messages'][0])) {
            $content['message'] = $content['messages'][0];
        }

        unset($content['messages']);
    }


}
