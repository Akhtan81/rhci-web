<?php

namespace App\Service;

use App\Entity\CategoryType;
use App\Entity\Order;
use App\Entity\Partner;
use App\Entity\Payment;
use App\Entity\PaymentStatus;
use App\Entity\PaymentType;
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

        var_dump($content);

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

    private function getPayerCredentials(Order $order)
    {
        $env = $this->container->getParameter('payment_environment');
        $trans = $this->container->get('translator');

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


        if ($env !== 'prod') {
            $payer = 'tok_visa';
        }

        return $payer;
    }

    public function createPayment(Order $order, $currency = 'usd')
    {
        $minimalPaymentAmount = intval($this->container->getParameter('minimal_payment_amount'));
        $secret = $this->container->getParameter('stripe_client_secret');

        $em = $this->container->get('doctrine')->getManager();

        $price = max($minimalPaymentAmount, $order->getPrice());

        $payer = $this->getPayerCredentials($order);

        $payment = new Payment();
        $payment->setOrder($order);
        $payment->setPrice($price);
        $payment->setStatus(PaymentStatus::CREATED);

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

    public function createRefund(Order $order, $price, $currency = 'usd')
    {
//        $minimalPaymentAmount = intval($this->container->getParameter('minimal_payment_amount'));
        $secret = $this->container->getParameter('stripe_client_secret');

        $em = $this->container->get('doctrine')->getManager();

//        $price = max($minimalPaymentAmount, $order->getPrice());

        $payer = $this->getPayerCredentials($order);

        $payment = new Payment();
        $payment->setType(PaymentType::REFUND);
        $payment->setOrder($order);
        $payment->setPrice($price);
        $payment->setStatus(PaymentStatus::CREATED);

        if ($secret) {
            \Stripe\Stripe::setApiKey($secret);

            $refund = \Stripe\Refund::create([
                'source' => $payer,
                'amount' => $payment->getPrice(),
                'currency' => $currency,
                'description' => 'Order #' . $order->getId()
            ]);

            $response = json_encode($refund->jsonSerialize());

            $status = is_null($refund->failure_code) && $refund->paid === true && $refund->status === 'succeeded'
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