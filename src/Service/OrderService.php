<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\District;
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

        $entity = new Order();
        $entity->setUser($user);

        $this->update($entity, $content);

        return $entity;
    }

    /**
     * @param Order $entity
     * @param $content
     *
     * @throws \Exception
     */
    private function update(Order $entity, $content)
    {
        $em = $this->container->get('doctrine')->getManager();
        $user = $this->container->get(UserService::class)->getUser();

        $now = new \DateTime();

        $entity->setUpdatedAt($now);
        $entity->setUpdatedBy($user);

        if (isset($content['scheduledAt'])) {
            $date = \DateTime::createFromFormat('Y-m-d H:i', $content['scheduledAt']);
            if (!$date) {
                throw new \Exception('Invalid scheduledAt', 422);
            }

            $entity->setScheduledAt($date);
        }

        if (isset($content['isScheduledApproved'])) {
            $entity->setIsScheduledApproved($content['isScheduledApproved'] === true);
        }

        if (isset($content['locationLng'])) {
            $entity->setLocationLng($content['locationLng']);
        }

        if (isset($content['locationLat'])) {
            $entity->setLocationLat($content['locationLat']);
        }

        if (isset($content['repeatable'])) {
            switch ($content['repeatable']) {
                case OrderRepeat::MONTH:
                case OrderRepeat::MONTH_3:
                case OrderRepeat::WEEK:
                    $entity->setRepeatable($content['repeatable']);
                    break;
                default:
                    throw new \Exception('Unknown order repeat type', 422);
            }
        }

        if (isset($content['status'])) {
            switch ($content['status']) {
                case OrderStatus::CREATED:
                case OrderStatus::REJECTED:
                case OrderStatus::APPROVED:
                case OrderStatus::DONE:
                case OrderStatus::IN_PROGRESS:
                    $entity->setStatus($content['status']);
                    break;
                default:
                    throw new \Exception('Unknown order status', 422);
            }
        }

        if (isset($content['district'])) {
            /** @var District $district */
            $district = $em->getRepository(District::class)->find($content['district']);
            if (!$district) {
                throw new \Exception('District was not found', 404);
            }

            $this->handleDistrict($entity, $district);
        }

        if (isset($content['message'])) {
            $this->handleMessage($entity, $content['message']);
        }

        if (isset($content['items'])) {
            $totalPrice = 0;
            foreach ($content['items'] as $item) {
                $orderItem = $this->handleOrderItem($entity, $item);

                $totalPrice += $orderItem->getPrice();
            }

            $entity->setPrice($totalPrice);
        }

        $em->persist($entity);
        $em->flush();
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

        $item = new OrderItem();
        $item->setOrder($entity);
        $item->setQuantity($content['quantity']);

        /** @var Category $category */
        $category = $em->getRepository(Category::class)->find($content['category']);
        if (!$category) {
            throw new \Exception('Category was not found', 404);
        }

        $item->setCategory($category);

        if ($category->hasPrice()) {
            $item->setPrice($item->getQuantity() * $category->getPrice());
        }

        $em->persist($item);

        $entity->addItem($item);

        return $item;
    }

    private function handleDistrict(Order $entity, District $district)
    {
        $entity->setDistrict($district);
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
                throw new \Exception('Media was not found', 404);
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


}