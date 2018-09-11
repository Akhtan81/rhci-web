<?php

namespace App\Service;

use App\Entity\CategoryType;
use App\Entity\Order;
use App\Entity\Partner;
use App\Entity\Payment;
use App\Entity\PaymentStatus;
use Symfony\Component\DependencyInjection\ContainerInterface;

class StripeService
{

    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function updateAccountId(Partner $partner, $authCode)
    {
        $secret = $this->container->getParameter('stripe_client_secret');

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://connect.stripe.com/oauth/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, implode('&', [
            'grant_type=authorization_code',
            'code=' . $authCode,
            'client_secret=' . $secret
        ]));

        $response = curl_exec($ch);

        curl_close($ch);

        $content = json_decode($response, true);

        if (isset($content['error'])) {
            throw new \Exception($content['error_description'], 500);
        }

        $accountId = $content['stripe_user_id'];

        $partnerService = $this->container->get(PartnerService::class);

        if ($partner && $accountId) {
            $partnerService->update($partner, [
                'accountId' => $accountId
            ]);
        }

    }

    public function createPayment(Order $order, $currency = 'usd')
    {
        $em = $this->container->get('doctrine')->getManager();
        $trans = $this->container->get('translator');

        $price = max(2500, $order->getPrice());

        $user = $order->getUser();
        $partner = $order->getPartner();

        switch ($order->getType()) {
            case CategoryType::RECYCLING:
                $payer = $partner->getAccountId();
                break;
            default:

                $card = $user->getPrimaryCreditCard();
                if (!$card) {
                    throw new \Exception($trans->trans('validation.not_found'), 404);
                }

                $payer = $card->getToken();
        }

        $payment = new Payment();
        $payment->setOrder($order);
        $payment->setPrice($price);
        $payment->setStatus(PaymentStatus::CREATED);

        $secret = $this->container->getParameter('stripe_client_secret');
        $env = $this->container->getParameter('payment_environment');

        if ($env !== 'prod') {
            $payer = 'tok_visa';
        }

        if ($secret) {
            \Stripe\Stripe::setApiKey($secret);

            $charge = \Stripe\Charge::create([
                'source' => $payer,
                'amount' => $payment->getPrice(),
                'currency' => $currency,
                'description' => 'Order #' . $order->getId()
            ]);

            $response = json_encode($charge->jsonSerialize());

            $status = is_null($charge->failure_code) && $charge->paid === true && $charge->status === 'succeeded'
                ? PaymentStatus::SUCCESS
                : PaymentStatus::FAILURE;

            $payment->setProviderResponse($response);
            $payment->setStatus($status);
        }

        $em->persist($payment);
        $em->flush();

        return $payment;
    }


}