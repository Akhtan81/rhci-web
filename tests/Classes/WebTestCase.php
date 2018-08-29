<?php

namespace App\Tests\Classes;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as TestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

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
     * @param string $login
     *
     * @return Client
     * @throws \Exception
     */
    protected function createAuthorizedClient($login)
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $em = $container->get('doctrine')->getManager();

        $session = $container->get('session');
        $firewallName = 'main';

        /** @var User $user */
        $user = $em->getRepository(User::class)->loadUserByUsername($login);
        if (!$user) {
            throw new \Exception('User was not found');
        }

        $token = new UsernamePasswordToken($user, null, $firewallName, $user->getRoles());

        $container->get('security.token_storage')->setToken($token);

        $token = $container->get('security.token_storage')->getToken();

        // save the login token into the session and put it in a cookie
        $session->set('_security_' . $firewallName, serialize($token));

        $session->save();

        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));

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
}