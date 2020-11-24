<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\CategoryTranslation;
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
use App\Entity\PartnerPostalCode;
use App\Entity\PartnerStatus;
use App\Entity\Payment;
use App\Entity\PaymentStatus;
use App\Entity\PaymentType;
use App\Entity\Unit;
use App\Entity\UnitTranslation;
use Gedmo\SoftDeleteable\Filter\SoftDeleteableFilter;
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
        $trans = $this->container->get('translator');
        $em = $this->container->get('doctrine')->getManager();
        $stripe = $this->container->get(PaymentService::class);
        $userService = $this->container->get(UserService::class);

        $canEditSensitiveInfo = $this->canEditSensitiveInfo();

        $now = new \DateTime();
        $entity = new Order();
        $user = null;
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

        $this->handleOrderPrice($entity);

        $this->update($entity, $content);

        $location = $entity->getLocation();
        $partner = $entity->getPartner();

        if (!$location || !$location->getPostalCode()) {
            $this->failOrderCreation($entity, $trans->trans('validation.order_location_not_found'));
        }

        if (!$partner) {
            $this->failOrderCreation($entity, $trans->trans('validation.partner_not_found'));
        }

        switch ($entity->getType()) {
            case CategoryType::SHREDDING:
            case CategoryType::JUNK_REMOVAL:
            case CategoryType::BUSYBEE:
            case CategoryType::MOVING:
                $stripe->checkHasCards($entity);
                break;
            case CategoryType::DONATION:
            case CategoryType::RECYCLING:
                break;
        }


        if (!$location->getCountry() && $partner->getCountry()) {
            $location->setCountry($partner->getCountry());
            $em->persist($location);
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
        $userService = $this->container->get(UserService::class);
        $user = $userService->getUser();
        $pushService = $this->container->get(PushService::class);

        $isAdmin = $userService->getAdmin();
        $canEditSensitiveInfo = $this->canEditSensitiveInfo();
        $isOrderCanceled = false;
        $isOrderInProgress = false;
        $isOrderDone = false;

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

            $entity->setScheduledAt($date);
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

                if ($isAdmin) {
                    if ($entity->getId()) {
                        if (isset($content['price'])) {
                            $entity->setPrice($content['price']);
                        }
                    }
                }

                break;
            default:
                if ($isOrderCanceled) {
                    $this->makeFullRefund($entity);
                }
        }

        if (isset($content['status'])) {
            $isOrderCanceled = $content['status'] === OrderStatus::CANCELED
                && $entity->getStatus() !== OrderStatus::CANCELED;

            $isOrderInProgress = $content['status'] === OrderStatus::IN_PROGRESS
                && $entity->getStatus() !== OrderStatus::IN_PROGRESS;

            $isOrderDone = $content['status'] === OrderStatus::DONE
                && $entity->getStatus() !== OrderStatus::DONE;

            $this->handleStatusChange($entity, $content['status']);
        }

        if ($isOrderDone) {
            $this->chargeCustomer($entity);
        }

        $entity->setPayments($entity->getPayments());

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

    private function chargeCustomer(Order $entity)
    {
        $paymentService = $this->container->get(PaymentService::class);

        $currency = $entity->getPartner()->getCountry()->getCurrency();
        $price = $entity->getPrice();

        if ($price > 0) {
            $payment = $paymentService->createPayment($entity, $price, $currency);
            $entity->getPayments()->add($payment);
        }
    }

    private function makeFullRefund(Order $entity)
    {
        $paymentService = $this->container->get(PaymentService::class);

        $price = $entity->getPrice();
        if ($price > 0) {
            $payments = $paymentService->findByFilter([
                'status' => PaymentStatus::SUCCESS,
                'order' => $entity->getId()
            ]);
            if(!is_null($payments)){
                /** @var Payment $payment */
                foreach ($payments as $payment) {
                    if ($payment->isRefunded()) continue;

                    $refund = $paymentService->createRefund($payment, $price, false);

                    if ($refund) {
                        $entity->getPayments()->add($refund);
                    }
                }
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
            $price = $partnerCategory->getPrice() ?? 0;

            $item->setPrice($price);

            $totalPrice += $price * $item->getQuantity();

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
            case CategoryType::BUSYBEE:
                if (!$partner->canManageBusyBeeOrders()) {
                    $this->failOrderCreation($entity, $trans->trans('validation.partner_cannot_manage_order'));
                }
                break;
            case CategoryType::MOVING:
                if (!$partner->canManageMovingOrders()) {
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

        /** @var SoftDeleteableFilter $soft */
        $soft = $em->getFilters()->getFilter('softdeleteable');

        $soft->disableForEntity(PartnerCategory::class);
        $soft->disableForEntity(PartnerPostalCode::class);
        $soft->disableForEntity(Unit::class);
        $soft->disableForEntity(UnitTranslation::class);
        $soft->disableForEntity(Category::class);
        $soft->disableForEntity(CategoryTranslation::class);

        $items =  $em->getRepository(Order::class)->countByFilter($filter);

        $soft->enableForEntity(PartnerCategory::class);
        $soft->enableForEntity(PartnerPostalCode::class);
        $soft->enableForEntity(Unit::class);
        $soft->enableForEntity(UnitTranslation::class);
        $soft->enableForEntity(Category::class);
        $soft->enableForEntity(CategoryTranslation::class);

        return $items;
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

        /** @var SoftDeleteableFilter $soft */
        $soft = $em->getFilters()->getFilter('softdeleteable');

        $soft->disableForEntity(PartnerCategory::class);
        $soft->disableForEntity(PartnerPostalCode::class);
        $soft->disableForEntity(Unit::class);
        $soft->disableForEntity(UnitTranslation::class);
        $soft->disableForEntity(Category::class);
        $soft->disableForEntity(CategoryTranslation::class);

        $items = $em->getRepository(Order::class)->findByFilter($filter, $page, $limit);

        $soft->enableForEntity(PartnerCategory::class);
        $soft->enableForEntity(PartnerPostalCode::class);
        $soft->enableForEntity(Unit::class);
        $soft->enableForEntity(UnitTranslation::class);
        $soft->enableForEntity(Category::class);
        $soft->enableForEntity(CategoryTranslation::class);

        return $items;
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

    public function serialize($content, $locale, $groups = [])
    {
        $result = json_decode($this->container->get('jms_serializer')
            ->serialize($content, 'json', SerializationContext::create()
                ->setGroups(array_merge(['api_v1'], $groups))), true);

        if ($content instanceof Order) {
            $this->onPostSerialize($result, $locale);
        } else {
            foreach ($result as &$item) {
                $this->onPostSerialize($item, $locale);
            }
        }

        return $result;
    }

    public function serializeV2($content, $locale)
    {
        return $this->serialize($content, $locale, ['api_v2']);
    }

    private function onPostSerialize(&$content, $locale)
    {
        $trans = $this->container->get('translator');
        $countryService = $this->container->get(CountryService::class);
        $categoryService = $this->container->get(CategoryService::class);
        $partnerService = $this->container->get(PartnerService::class);
        $partnerCategoryService = $this->container->get(PartnerCategoryService::class);

        if (isset($content['partner'])) {
//            $partnerService->onPostSerialize($content['partner'], $locale);

            if (isset($content['partner']['requests'])) {
                unset($content['partner']['requests']);
            }

            if (isset($content['partner']['requestedCategories'])) {
                unset($content['partner']['requestedCategories']);
            }
        }

        if (isset($content['messages'][0])) {
            $content['message'] = $content['messages'][0];
        }

        unset($content['messages']);

        if (isset($content['location']['country'])) {
            $countryService->onPostSerialize($content['location']['country'], $locale);

            // overwrite Country object to string
            if (isset($content['location']['country']['name'])) {
                $content['location']['country'] = $content['location']['country']['name'];
            }
        }

        if (isset($content['items'])) {

            foreach ($content['items'] as &$item) {
                if (isset($item['category'])) {
                    $categoryService->onPostSerialize($item['category'], $locale);
                }

                if (isset($item['partnerCategory'])) {
                    $partnerCategoryService->onPostSerialize($item['partnerCategory'], $locale);
                }
            }
        }

        if (isset($content['type'])) {
            $content['type'] = [
                'key' => $content['type'],
                'locale' => $locale,
                'name' => $trans->trans('order_types.' . $content['type'], [], 'messages', $locale),
            ];
        }
    }


}
