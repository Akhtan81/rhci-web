<?php

namespace App\Service;

use App\Entity\Location;
use App\Entity\Media;
use App\Entity\User;
use JMS\Serializer\SerializationContext;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserService
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
     * @return User
     * @throws \Exception
     */
    public function create($content)
    {
        $entity = new User();
        $entity->setIsActive(true);

        if (isset($content['email'])) {
            $entity->setEmail(mb_strtolower(trim($content['email']), 'utf8'));
        }

        $this->update($entity, $content);

        return $entity;
    }

    /**
     * @param User $entity
     * @param $content
     *
     * @throws \Exception
     */
    public function update(User $entity, $content)
    {
        $em = $this->container->get('doctrine')->getManager();
        $encoder = $this->container->get('security.password_encoder');
        $trans = $this->container->get('translator');
        $currentUser = $this->container->get(UserService::class)->getUser();

        if (isset($content['name'])) {
            $entity->setName($content['name']);
        }

        if (isset($content['isActive'])) {
            $isActive = $content['isActive'] === true;

            $entity->setIsActive($isActive);
        }

        if (isset($content['password'])) {

            if ($currentUser && !$currentUser->isAdmin() && isset($content['currentPassword'])) {
                $isValid = $encoder->isPasswordValid($entity, $content['currentPassword']);
                if (!$isValid) {
                    throw new \Exception($trans->trans('validation.current_password_mismatch'), 400);
                }
            }

            $password = $encoder->encodePassword($entity, $content['password']);
            $entity->setPassword($password);
        }

        if (isset($content['avatar'])) {
            /** @var Media $media */
            $media = $em->getRepository(Media::class)->find($content['avatar']);
            if (!$media) {
                throw new \Exception($trans->trans('validation.not_found'), 404);
            }
            $entity->setAvatar($media);
        }

        if (isset($content['location'])) {
            $this->handleLocation($entity, $content['location']);
        }

        $this->validate($entity);

        $em->persist($entity);
        $em->flush();
    }

    private function handleLocation(User $user, $content)
    {
        $em = $this->container->get('doctrine')->getManager();

        $location = new Location();
        $location->setUser($user);

        if (isset($content['lat'])) {
            $location->setLat($content['lat']);
        }

        if (isset($content['lng'])) {
            $location->setLng($content['lng']);
        }

        if (isset($content['address'])) {
            $location->setAddress($content['address']);
        }

        $user->setLocation($location);
        $user->getLocations()->add($location);

        $em->persist($location);
    }

    /**
     * @param User $entity
     *
     * @throws \Exception
     */
    private function validate(User $entity)
    {
        $em = $this->container->get('doctrine')->getManager();
        $trans = $this->container->get('translator');

        if ($entity->getEmail()) {
            $match = $em->getRepository(User::class)->findOneBy([
                'email' => mb_strtolower($entity->getEmail(), 'utf8'),
            ]);
            if ($match && $match !== $entity) {
                throw new \Exception($trans->trans('validation.email_reserved'), 400);
            }
        }

        if ($entity->getPhone()) {
            $match = $em->getRepository(User::class)->findOneBy([
                'phone' => $entity->getPhone()
            ]);
            if ($match && $match !== $entity) {
                throw new \Exception($trans->trans('validation.phone_reserved'), 400);
            }
        }
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

        return $em->getRepository(User::class)->countByFilter($filter);
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

        return $em->getRepository(User::class)->findByFilter($filter, $page, $limit);
    }

    /**
     * @param array $filter
     *
     * @return null|User
     */
    public function findOneByFilter(array $filter)
    {
        $items = $this->findByFilter($filter, 1, 1);
        if (count($items) !== 1) return null;

        return $items[0];
    }

    /**
     * @return User|null
     */
    public function getUser()
    {
        if (!$this->container->has('security.token_storage')) {
            throw new \LogicException('The SecurityBundle is not registered in your application. Try running "composer require symfony/security-bundle".');
        }

        if (null === $token = $this->container->get('security.token_storage')->getToken()) {
            return null;
        }

        if (!is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return null;
        }

        return $user;
    }

    public function serialize($content)
    {
        return json_decode($this->container->get('jms_serializer')
            ->serialize($content, 'json', SerializationContext::create()
                ->setGroups(['api_v1', 'api_v1_user'])), true);
    }


}