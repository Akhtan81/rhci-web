<?php

namespace App\Service;

use App\Entity\CategoryType;
use App\Entity\Order;
use App\Entity\Partner;
use App\Entity\Payment;
use App\Entity\PaymentStatus;
use App\Entity\PaymentType;
use JMS\Serializer\SerializationContext;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PaymentService
{

    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function update(Payment $payment, $content = null, $flush = true) {

        $em = $this->container->get('doctrine')->getManager();
        $userService = $this->container->get(UserService::class);

        $isAdmin = $userService->getAdmin();

        $payment->setUpdatedAt(new \DateTime());

        if ($isAdmin) {
            if (isset($content['status'])) {
                $this->handleStatus($payment, $content['status']);
            }
        }

        $em->persist($payment);
        $flush && $em->flush();
    }

    public function handleStatus(Payment $payment, $status)
    {
        $trans = $this->container->get('translator');

        switch ($payment->getStatus()) {
            case PaymentStatus::CREATED:

                switch ($status) {
                    case PaymentStatus::CREATED:
                    case PaymentStatus::FAILURE:
                    case PaymentStatus::SUCCESS:

                        $payment->setStatus($status);

                        break;
                    default:
                        throw new \Exception($trans->trans('validation.invalid_payment_status'), 400);
                }

                break;
        }
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
     * @return string
     * @throws \Exception
     */
    private function getPayerCredentials(Order $order)
    {
        $trans = $this->container->get('translator');

        $user = $order->getUser();

        switch ($order->getType()) {
            case CategoryType::DONATION:
            case CategoryType::RECYCLING:

                return null;

            default:

                $payer = $user->getCustomerId();
                if (!$payer) {
                    throw new \Exception($trans->trans('validation.not_found'), 404);
                }
        }

        return $payer;
    }

    /**
     * @param Order $order
     *
     * @return string
     * @throws \Exception
     */
    private function getRecipientCredentials(Order $order)
    {
        $trans = $this->container->get('translator');
        $partner = $order->getPartner();

        switch ($order->getType()) {
            case CategoryType::DONATION:
            case CategoryType::RECYCLING:

                return null;

            default:
                $payer = $partner->getAccountId();
                if (!$payer) {
                    throw new \Exception($trans->trans('validation.no_partner_account_id'), 404);
                }
        }

        return $payer;
    }

    /**
     * @param Order $order
     * @param $price
     * @param $currency
     * @param bool $flush
     *
     * @return Payment
     * @throws \Exception
     */
    public function createPayment(Order $order, $price, $currency, $flush = true)
    {
        $secret = $this->container->getParameter('stripe_client_secret');
        $trans = $this->container->get('translator');

        $em = $this->container->get('doctrine')->getManager();

        $payment = new Payment();
        $payment->setOrder($order);
        $payment->setPrice($price);
        $payment->setCurrency($currency);
        $payment->setStatus(PaymentStatus::CREATED);

        if ($secret) {

            $payer = $this->getPayerCredentials($order);
            $recipient = $this->getRecipientCredentials($order);

            //Ignore created payment
            if (!($payer && $recipient)) return null;

            \Stripe\Stripe::setApiKey($secret);

            try {
                $totalSum = $payment->getPrice();
                $subtractedSum = $this->getOrderSum($totalSum);

                $charge = \Stripe\Charge::create([
                    'customer' => $payer,
                    'amount' => $totalSum,
                    'currency' => mb_strtolower($payment->getCurrency(), 'utf8'),
                    'description' => 'Order #' . $order->getId(),
                    "destination" => [
                        'amount' => $subtractedSum,
                        "account" => $recipient,
                    ],
                ]);

                $response = json_encode($charge->jsonSerialize());

                $status = $charge->status === 'succeeded'
                    ? PaymentStatus::SUCCESS
                    : PaymentStatus::FAILURE;

                $payment->setProviderResponse($response);
                $payment->setStatus($status);

            } catch (\Exception $e) {

                throw new \Exception($trans->trans('payments.invalid_payment', [
                    '__MSG__' => $e->getMessage()
                ]));
            }

        } else {
            if (!$this->isProd()) {
                $payment->setStatus(PaymentStatus::SUCCESS);
            }
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

        $payment = new Payment();
        $payment->setType(PaymentType::REFUND);
        $payment->setStatus(PaymentStatus::CREATED);
        $payment->setOrder($rootPayment->getOrder());
        $payment->setPrice($price);
        $payment->setCurrency($rootPayment->getCurrency());

        if ($payment->getPrice() > $rootPayment->getPrice()) {
            throw new \Exception($trans->trans('validation.invalid_refund_amount'), 400);
        }

        if ($secret) {
            \Stripe\Stripe::setApiKey($secret);

            $id = $rootPayment->getChargeId();
            if (!$id) {
                throw new \Exception($trans->trans('validation.payment_has_no_charge_id'), 404);
            }

            try {
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
            } catch (\Exception $e) {

                throw new \Exception($trans->trans('payments.invalid_refund', [
                    '__MSG__' => $e->getMessage()
                ]));
            }
        } else {
            if (!$this->isProd()) {
                $payment->setStatus(PaymentStatus::SUCCESS);
            }
        }

        $rootPayment->setRefunded($payment->getStatus() === PaymentStatus::SUCCESS);

        $em->persist($payment);
        $em->persist($rootPayment);

        $flush && $em->flush();

        return $payment;
    }

    private function isProd()
    {
        return $this->container->getParameter('kernel.environment') === 'prod';
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

    public function getOrderSum($totalSum)
    {
        return $totalSum - $this->getStripeFee($totalSum) - $this->getMobilerecyclingFee($totalSum);
    }

    private function getStripeFee($sum)
    {
        return (int) ceil(0.029 * $sum) + 30;
    }

    private function getMobilerecyclingFee($sum)
    {
        return (int) ceil(0.05 * $sum);
    }

    public function serialize($content)
    {
        return json_decode($this->container->get('jms_serializer')
            ->serialize($content, 'json', SerializationContext::create()
                ->setGroups(['api_v1', 'api_v2'])), true);
    }


}
