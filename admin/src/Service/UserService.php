<?php

namespace App\Service;

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
     * @param bool $flush
     *
     * @return User
     * @throws \Exception
     */
    public function create($content, $flush = true)
    {
        $entity = new User();
        $entity->setIsActive(true);

        $this->update($entity, $content, $flush);

        return $entity;
    }

    /**
     * @param User $entity
     * @param $content
     *
     * @param bool $flush
     *
     * @throws \Exception
     */
    public function update(User $entity, $content, $flush = true)
    {
        $em = $this->container->get('doctrine')->getManager();
        $encoder = $this->container->get('security.password_encoder');
        $trans = $this->container->get('translator');
        $currentUser = $this->container->get(UserService::class)->getUser();
        $creditCardService = $this->container->get(CreditCardService::class);

        $isAdmin = $currentUser && $currentUser->isAdmin();

        if (isset($content['email'])) {
            $entity->setEmail(mb_strtolower(trim($content['email']), 'utf8'));
        }

        if (isset($content['name'])) {
            $entity->setName($content['name']);
        }

        if (isset($content['phone'])) {
            $entity->setPhone($content['phone']);
        }

        if ($isAdmin && isset($content['isActive'])) {
            $isActive = $content['isActive'] === true;

            $entity->setIsActive($isActive);
        }

        if (isset($content['password'])) {

            if ($currentUser && !$isAdmin) {

                if (!isset($content['currentPassword'])) {
                    throw new \Exception($trans->trans('validation.bad_request'), 400);
                }

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

        if (isset($content['creditCards']) && count($content['creditCards']) > 0) {

            $entity->setPrimaryCreditCard(null);

            $entity->getCreditCards()->clear();

            foreach ($content['creditCards'] as $creditCard) {
                $card = $creditCardService->create($entity, $creditCard, false);

                $entity->getCreditCards()->add($card);
            }

            if (!$entity->getPrimaryCreditCard()) {
                $entity->setPrimaryCreditCard($entity->getCreditCards()->get(0));
            }
        }

        $this->validate($entity);

        $this->createCustomer($entity);

        $em->persist($entity);

        $flush && $em->flush();
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

    public function createCustomer(User $user)
    {
        $secret = $this->container->getParameter('stripe_client_secret');
        $trans = $this->container->get('translator');

        if ($user->getCustomerId()) return;

        if ($secret) {
            \Stripe\Stripe::setApiKey($secret);

            try {
                $customer = \Stripe\Customer::create([
                    "email" => $user->getEmail(),
                ]);

                $response = json_encode($customer->jsonSerialize());

                $user->setCustomerResponse($response);
                $user->setCustomerId($customer->id);

            } catch (\Exception $e) {

                throw new \Exception($trans->trans('stripe.invalid_customer_from_user', [
                    '__MSG__' => $e->getMessage()
                ]));
            }
        } else {
            $user->setCustomerId("test");
        }
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

    /**
     * @return \App\Entity\Partner|null
     */
    public function getPartner()
    {
        $user = $this->getUser();
        return $user ? $user->getPartner() : null;
    }

    /**
     * @return \App\Entity\User|null
     */
    public function getAdmin()
    {
        $user = $this->getUser();
        return $user && $user->isAdmin() ? $user : null;
    }

    public function serialize($content)
    {
        $result = json_decode($this->container->get('jms_serializer')
            ->serialize($content, 'json', SerializationContext::create()
                ->setGroups(['api_v1', 'api_v1_user'])), true);

        return $result;
    }

    public function serializeV2($content)
    {
        return json_decode($this->container->get('jms_serializer')
            ->serialize($content, 'json', SerializationContext::create()
                ->setGroups(['api_v1', 'api_v1_user', 'api_v2'])), true);
    }


}