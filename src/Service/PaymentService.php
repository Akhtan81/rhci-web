<?php

namespace App\Service;

use App\Entity\CategoryType;
use App\Entity\Order;
use App\Entity\Partner;
use App\Entity\Payment;
use App\Entity\PaymentStatus;
use App\Entity\PaymentType;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PaymentService
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

    /**
     * @param Order $order
     *
     * @return null|string
     * @throws \Exception
     */
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

    /**
     * @param Order $order
     * @param $price
     * @param bool $flush
     *
     * @return Payment
     * @throws \Exception
     */
    public function createPayment(Order $order, $price, $flush = true)
    {
        $secret = $this->container->getParameter('stripe_client_secret');

        $em = $this->container->get('doctrine')->getManager();

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
                'currency' => 'usd',
                'description' => 'Order #' . $order->getId()
            ]);

            $response = json_encode($charge->jsonSerialize());

            $status = $charge->status === 'succeeded'
                ? PaymentStatus::SUCCESS
                : PaymentStatus::FAILURE;

            $payment->setProviderResponse($response);
            $payment->setStatus($status);
        }

        $em->persist($payment);

        $flush && $em->flush();

        return $payment;
    }

    /**
     * @param Payment $rootPayment
     * @param $price
     * @param bool $flush
     *
     * @return Payment
     * @throws \Exception
     */
    public function createRefund(Payment $rootPayment, $price, $flush = true)
    {
        $secret = $this->container->getParameter('stripe_client_secret');
        $trans = $this->container->get('translator');

        $em = $this->container->get('doctrine')->getManager();

        $id = $rootPayment->getChargeId();
        if (!$id) {
            throw new \Exception($trans->trans('validation.not_found'), 404);
        }

        $payment = new Payment();
        $payment->setType(PaymentType::REFUND);
        $payment->setOrder($rootPayment->getOrder());
        $payment->setPrice($price);
        $payment->setStatus(PaymentStatus::CREATED);

        if ($secret) {
            \Stripe\Stripe::setApiKey($secret);

            $refund = \Stripe\Refund::create([
                'charge' => $id,
                'amount' => $payment->getPrice(),
            ]);

            $response = json_encode($refund->jsonSerialize());

            $status = $refund->status === 'succeeded'
                ? PaymentStatus::SUCCESS
                : PaymentStatus::FAILURE;

            $payment->setProviderResponse($response);
            $payment->setStatus($status);
        }

        $em->persist($payment);

        $flush && $em->flush();

        return $payment;
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

        return $em->getRepository(Payment::class)->countByFilter($filter);
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

        return $em->getRepository(Payment::class)->findByFilter($filter, $page, $limit);
    }

    /**
     * @param array $filter
     *
     * @return null|Payment
     */
    public function findOneByFilter(array $filter)
    {
        $items = $this->findByFilter($filter, 1, 1);
        if (count($items) !== 1) return null;

        return $items[0];
    }


}