<?php

namespace App\Tests\Classes;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;

abstract class WebTestCase extends TestCase
{

    public function isRedirect(Response $response, $location)
    {
        return in_array($response->getStatusCode(), array(201, 301, 302, 303, 307, 308))
            && strpos($response->headers->get('Location'), $location) !== false;
    }

    /**
     * @return Client
     */
    protected function createUnauthorizedClient()
    {
        $client = self::createClient();
        $client->followRedirects(false);

        return $client;
    }

    /**
     * @param $login
     *
     * @return string
     * @throws \Exception
     */
    public function getAccessToken($login)
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $em = $container->get('doctrine')->getManager();

        /** @var User $user */
        $user = $em->getRepository(User::class)->loadUserByUsername($login);
        if (!$user) {
            throw new \Exception('User was not found');
        }

        return $user->getAccessToken();
    }

    /**
     * @param string $login
     *
     * @param string $firewallName
     *
     * @return Client
     * @throws \Exception
     */
    protected function createAuthorizedClient($login, $firewallName = 'main')
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $em = $container->get('doctrine')->getManager();

        /** @var User $user */
        $user = $em->getRepository(User::class)->loadUserByUsername($login);
        if (!$user) {
            throw new \Exception('User was not found');
        }

        $token = new PreAuthenticatedToken(
            $user,
            $user->getAccessToken(),
            $firewallName,
            $user->getRoles()
        );

        $container->get('security.token_storage')->setToken($token);

        return $client;
    }

    /**
     * @return Client
     * @throws \Exception
     */
    protected function createAuthorizedAdmin()
    {
        return $this->createAuthorizedClient('admin');
    }

    /**
     * @return Client
     * @throws \Exception
     */
    protected function createAuthorizedPartner()
    {
        return $this->createAuthorizedClient('partner');
    }

    /**
     * @return Client
     * @throws \Exception
     */
    protected function createAuthorizedUser()
    {
        return $this->createAuthorizedClient('user');
    }

    protected function getUserAccessToken()
    {
        return $this->getAccessToken('user');
    }

    protected function getAdminAccessToken()
    {
        return $this->getAccessToken('admin');
    }

    protected function getPartnerAccessToken()
    {
        return $this->getAccessToken('partner');
    }
}