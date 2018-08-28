<?php

namespace App\Service;

use App\Entity\User;
use App\Event\StudentActivatedEvent;
use JMS\Serializer\SerializationContext;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
     * @param UploadedFile $image
     *
     * @return User
     * @throws \Exception
     */
    public function create($content, UploadedFile $image)
    {
        $entity = new User();

        if (isset($content['email'])) {
            $entity->setEmail(mb_strtolower(trim($content['email']), 'utf8'));
        }

        $this->update($entity, $content, $image);

        return $entity;
    }

    /**
     * @param User $entity
     * @param $content
     * @param UploadedFile|null $image
     *
     * @throws \Exception
     */
    public function update(User $entity, $content, UploadedFile $image = null)
    {
        $em = $this->container->get('doctrine')->getManager();
        $encoder = $this->container->get('security.password_encoder');
        $dispatcher = $this->container->get('event_dispatcher');
        $trans = $this->container->get('translator');
        $currentUser = $this->container->get(UserService::class)->getUser();

        $canActivate = false;

        if (isset($content['name'])) {
            $entity->setName($content['name']);
        }

        if (isset($content['username'])) {
            $entity->setUsername($content['username']);
        }

        if (isset($content['isActive'])) {
            $isActive = $content['isActive'] === true;

            $canActivate = !$entity->isActive() && $isActive;

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

        $this->validate($entity);

        if ($image) {
            $this->handleAvatar($entity, $image);
        }

        $em->persist($entity);
        $em->flush();

        if ($canActivate) {
            $dispatcher->dispatch(StudentActivatedEvent::NAME, new StudentActivatedEvent($entity));
        }
    }

    private function handleAvatar(User $entity, UploadedFile $image)
    {
        $root = $this->container->getParameter('kernel.project_dir') . '/public';
        $imageDirectory = $this->container->getParameter('upload_image_dir');

        $name = md5(uniqid()) . '.' . $image->getClientOriginalExtension();

        $image->move($root . $imageDirectory, $name);

        $entity->setAvatar($imageDirectory . '/' . $name);
    }

    private function validate(User $entity)
    {
        $em = $this->container->get('doctrine')->getManager();
        $trans = $this->container->get('translator');

        $match = $em->getRepository(User::class)->findOneBy([
            'email' => mb_strtolower($entity->getEmail(), 'utf8'),
        ]);
        if ($match && $match !== $entity) {
            throw new \Exception($trans->trans('validation.email_reserved'), 400);
        }

        $match = $em->getRepository(User::class)->findOneBy([
            'username' => mb_strtolower($entity->getUsername(), 'utf8'),
        ]);
        if ($match && $match !== $entity) {
            throw new \Exception($trans->trans('validation.username_reserved'), 400);
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
     * @param $id
     *
     * @return null|User
     */
    public function find($id)
    {
        $em = $this->container->get('doctrine')->getManager();

        return $em->getRepository(User::class)->find($id);
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
                ->setGroups(['api_v1'])), true);
    }


}