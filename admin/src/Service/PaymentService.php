<?php

namespace App\Service;

use App\Entity\CategoryType;
use App\Entity\Order;
use App\Entity\Partner;
use App\Entity\User;
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
        $isEnabled = $this->container->getParameter('stripe_enabled');
        if (!$isEnabled) return;

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

    public function updateAccountIdForUser(User $target, $authCode)
    {
        $isEnabled = $this->container->getParameter('stripe_enabled');
        if (!$isEnabled) return;

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

        $service = $this->container->get(UserService::class);

        if ($target && $accountId) {
            $service->update($target, [
                'accountId' => $accountId
            ]);
            /*$em = $this->container->get('doctrine')->getManager();
            $query = $em->getRepository(User::class)->createQueryBuilder('')
                ->update(User::class, 'u')
                ->set('u.accountId', ':accountId')
                ->setParameter('accountId', $accountId)
                ->where('u.id = :id')
                ->setParameter('id', $target->getId())
                ->getQuery();
            $result = $query->execute();*/
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

        $payer = $user->getCustomerId();
        if (!$payer) {
            throw new \Exception($trans->trans('validation.not_found'), 404);
        }

        return $payer;
    }

    private function getPayerAccountId(Order $order)
    {
        $trans = $this->container->get('translator');

        $user = $order->getUser();

        $payer = $user->getAccountId();
        if (!$payer) {
            throw new \Exception($trans->trans('validation.no_client_account_id'), 404);
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

        $payer = $partner->getAccountId();
        if (!$payer) {
            throw new \Exception($trans->trans('validation.no_partner_account_id'), 404);
        }

        return $payer;
    }

    private function getRecipientCustomerId(Order $order)
    {
        $trans = $this->container->get('translator');
        $partner = $order->getPartner();

        $payer = $partner->getCustomerId();
        if (!$payer) {
            throw new \Exception($trans->trans('validation.no_partner_account_id'), 404);
        }

        return $payer;
    }

    public function checkHasCards(Order $order)
    {
        $isEnabled = $this->container->getParameter('stripe_enabled');
        $secret = $this->container->getParameter('stripe_client_secret');
        $trans = $this->container->get('translator');
        $em = $this->container->get('doctrine')->getManager();

        if ($isEnabled && $secret) {
            $payer = $this->getPayerCredentials($order);
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/payment_methods?customer='.$payer.'&type=card');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERPWD, $secret . ':' . '');

            $headers = array();
            $headers[] = 'Stripe-Version: 2020-03-02';
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                throw new \Exception($trans->trans('validation.unable_to_request_from_stripe'), 404);
            }
            curl_close($ch);
            $res = json_decode($result);
            if(property_exists($res, 'data') && empty($res->data)){
                throw new \Exception($trans->trans('validation.no_attached_card'), 404);
            }
            /*$cardID = $customer->default_source;
            //$cardFromInvoice = $customer->invoice_settings->default_payment_method;
            if (!isset($cardID)){
                throw new \Exception($trans->trans('validation.no_attached_card'), 404);
            }*/
        }
    }

    private function sumBalance($balance, $payment)
    {
        if ($payment->getType() === PaymentType::PAYMENT) {
            $balance += $payment->getPrice();
        } else {
            $balance -= $payment->getPrice();
        }
        return $balance;
    }

    public function getOrderBalance(Order $order)
    {
        $em = $this->container->get('doctrine')->getManager();

        $payments = $this->findByFilter([
            'status' => PaymentStatus::SUCCESS,
            'order' => $order->getId()
        ]);
        return array_reduce($payments, "sumBalance", 0);
    }

    /**
     * @param Order $order
     * @param $price
     * @param $currency
     * @param bool $flush
     * @param bool $toClient
     *
     * @return Payment
     * @throws \Exception
     */
    public function createPayment(Order $order, $price, $currency, $flush = true, $toClient = false)
    {
        $isEnabled = $this->container->getParameter('stripe_enabled');
        $trans = $this->container->get('translator');
        $em = $this->container->get('doctrine')->getManager();

        $payment = new Payment();
        $payment->setOrder($order);
        $payment->setPrice($price);
        $payment->setCurrency($currency);
        $payment->setStatus(PaymentStatus::CREATED);
        $payment->setToClient($toClient);

        if ($isEnabled) {

            $secret = $this->container->getParameter('stripe_client_secret');
            
            if($toClient){
                //partner pays to client, thus payer(client) becomes recipient
                $recipient = $this->getPayerAccountId($order);
                $payer = $this->getRecipientCustomerId($order);
            }else{
                $payer = $this->getPayerCredentials($order);
                $recipient = $this->getRecipientCredentials($order);
            }
            //echo "secret: ".$secret.", payer: ".$payer.", recipient: ".$recipient; exit;
            //Ignore created payment
            if (!($payer && $recipient)) return null;

            if ($secret) {
                \Stripe\Stripe::setApiKey($secret);

                try {
                    //retrieve customer
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/customers/'.$payer);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                    curl_setopt($ch, CURLOPT_USERPWD, $secret . ':');

                    $headers = array();
                    $headers[] = 'Stripe-Version: 2020-03-02';
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                    $result = curl_exec($ch);
                    if (curl_errno($ch))
                        throw new \Exception($trans->trans('payments.invalid_payment', [
                        '__MSG__' => curl_error($ch)
                        ]));
                    curl_close($ch);
                    $customer = json_decode($result);
                    $pmid = $customer->invoice_settings->default_payment_method;
                    //echo "kirdi: ".$pmid; exit;
                    /*
curl https://api.stripe.com/v1/payment_intents \
 -H "Stripe-Version: 2020-03-02"
 -u sk_test_bng2j68HyMBYE5v2miAEapHj: \
 -d "payment_method_types[]"=card \
 -d customer=cus_HbsyVuX01dLCq9 \
 -d amount=1000 \
 -d currency=usd \
 -d confirm=true \
 -d payment_method=pm_payment-method-id \
 -d "transfer_data[destination]"="acct_1FcZisBKsGlwYWTQ" \
 -d "transfer_data[amount]"="1000" \
 -d description="Order #11"
                    */

                    $totalSum = $payment->getPrice();
                    $subtractedSum = $this->getPartnerAmount($totalSum);
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/payment_intents');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, 
                        "payment_method_types[]=card"
                        ."&customer=".$payer
                        ."&amount=".$totalSum
                        ."&payment_method=".$pmid
                        ."&confirm=true"
                        ."&currency=".mb_strtolower($payment->getCurrency(), 'utf8')
                        ."&transfer_data[destination]=".$recipient
                        ."&transfer_data[amount]=".$subtractedSum
                        ."&description=\"".'Order #' . $order->getId()."\""
                    );
                    curl_setopt($ch, CURLOPT_USERPWD, $secret . ':');

                    $headers = array();
                    $headers[] = 'Stripe-Version: 2020-03-02';
                    $headers[] = 'Content-Type: application/x-www-form-urlencoded';
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                    $result = curl_exec($ch);
                    if (curl_errno($ch)) {
                        throw new \Exception($trans->trans('payments.invalid_payment', [
                        '__MSG__' => curl_error($ch)
                        ]));
                    }
                    curl_close($ch);
                    //echo $result; exit;
                    $res = json_decode($result);
                    $status = $res->status === 'succeeded'
                    ? PaymentStatus::SUCCESS
                    : PaymentStatus::FAILURE;

                    $payment->setProviderResponse($result);
                    $payment->setStatus($status);

                    /*
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
                    $payment->setStatus($status);*/

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
        $isEnabled = $this->container->getParameter('stripe_enabled');
        $secret = $this->container->getParameter('stripe_client_secret');
        $trans = $this->container->get('translator');

        $em = $this->container->get('doctrine')->getManager();

        $stripeFee = $this->getStripeFee($price);
        $amountToReturn = $price - $stripeFee;

        $payment = new Payment();
        $payment->setType(PaymentType::REFUND);
        $payment->setStatus(PaymentStatus::CREATED);
        $payment->setOrder($rootPayment->getOrder());
        $payment->setPrice($amountToReturn);
        $payment->setCurrency($rootPayment->getCurrency());

        if ($payment->getPrice() > $rootPayment->getPrice()) {
            throw new \Exception($trans->trans('validation.invalid_refund_amount'), 400);
        }

        if ($payment->getPrice() <= 0) {
            throw new \Exception($trans->trans('validation.too_small_refund'), 400);
        }

        if ($isEnabled && $secret) {
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

    public function getPartnerAmount($totalSum)
    {
        return $totalSum - $this->getStripeFee($totalSum) - $this->getMobilerecyclingFee($totalSum);
    }

    private function getStripeFee($sum)
    {
        return (int) ceil(0.029 * $sum) + 30;
    }

    private function getMobilerecyclingFee($sum)
    {
        return (int) ceil(0.07 * $sum);
    }

    public function serialize($content)
    {
        return json_decode($this->container->get('jms_serializer')
            ->serialize($content, 'json', SerializationContext::create()
                ->setGroups(['api_v1', 'api_v2'])), true);
    }


}
