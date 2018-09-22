<?php

namespace App\Service;


use App\Entity\User;
use Mailgun\Mailgun;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EmailService
{

    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function sentResetPassword(User $user)
    {
        if (!$user->getEmail()) return;

        $client = $this->createClient();

        $trans = $this->container->get('translator');
        $twig = $this->container->get('twig');

        $subject = $trans->trans('email.reset_password.title');

        $body = $twig->render('emails/reset-password.html.twig', [
            'user' => $user
        ]);

        $domain = $this->container->getParameter('mailgun_domain');

        if ($domain) {
            $client->messages()->send($domain, [
                'from' => 'postmaster@' . $domain,
                'to' => $user->getEmail(),
                'subject' => $subject,
                'html' => $body
            ]);
        }
    }

    private function createClient()
    {
        return $this->container->get(Mailgun::class);
    }

}