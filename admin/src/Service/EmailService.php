<?php

namespace App\Service;

use App\Entity\Partner;
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

    public function onPartnerRejected(Partner $partner)
    {
        $trans = $this->container->get('translator');
        $twig = $this->container->get('twig');

        $subject = $trans->trans('email.partner_rejected.title');

        $body = $twig->render('emails/partner-rejected.html.twig', [
            'partner' => $partner
        ]);

        $this->send($partner->getUser()->getEmail(), $subject, $body);
    }

    public function onPartnerApproved(Partner $partner)
    {
        $trans = $this->container->get('translator');
        $twig = $this->container->get('twig');

        $subject = $trans->trans('email.partner_approved.title');

        $body = $twig->render('emails/partner-approved.html.twig', [
            'partner' => $partner
        ]);

        $this->send($partner->getUser()->getEmail(), $subject, $body);
    }

    public function sentResetPassword(User $user)
    {
        $trans = $this->container->get('translator');
        $twig = $this->container->get('twig');

        $subject = $trans->trans('email.reset_password.title');

        $body = $twig->render('emails/reset-password.html.twig', [
            'user' => $user
        ]);

        $this->send($user->getEmail(), $subject, $body);
    }

    private function send($email, $subject, $body)
    {
        $domain = $this->container->getParameter('mailgun_domain');
        $sender = $this->container->getParameter('mailgun_sender_name');
        $trans = $this->container->get('translator');

//        file_put_contents('/var/www/html/var/' . $subject . '.html', $body);

        if ($domain && $sender && $email) {

            $client = $this->container->get(Mailgun::class);

            try {
                $client->messages()->send($domain, [
                    'from' => $sender,
                    'to' => $email,
                    'subject' => $subject,
                    'html' => $body
                ]);
            } catch (\Exception $e) {
                throw new \Exception($trans->trans('validation.email_send_failed', [
                    '_MSG_' => $e->getMessage()
                ]), 500);
            }
        }
    }
}