<?php

namespace App\Tests\Controller;

use App\Entity\CategoryType;
use App\Entity\Partner;
use App\Entity\Payment;
use App\Entity\PaymentStatus;
use App\Tests\Classes\OrderCreator;
use App\Tests\Classes\UserCreator;
use App\Tests\Classes\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers \App\Controller\PaymentRESTController
 */
class PaymentRESTControllerTest extends WebTestCase
{

    use OrderCreator;
    use UserCreator;

    /**
     * @small
     */
    public function test_gets_unauthorized()
    {
        $client = $this->createUnauthorizedClient();

        $client->xmlHttpRequest('GET', "/api/v2/payments");

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /**
     * @small
     */
    public function test_gets_forbidden_user()
    {
        $client = $this->createAuthorizedUser();

        $client->xmlHttpRequest('GET', "/api/v2/payments");

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    /**
     * @small
     */
    public function test_put_unauthorized()
    {
        $client = $this->createUnauthorizedClient();

        $client->xmlHttpRequest('PUT', "/api/v2/payments/1");

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /**
     * @small
     */
    public function test_put_forbidden_user()
    {
        $client = $this->createAuthorizedUser();

        $client->xmlHttpRequest('PUT', "/api/v2/payments/1");

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    /**
     * @small
     */
    public function test_gets()
    {
        $client = $this->createAuthorizedAdmin();

        $client->xmlHttpRequest('GET', "/api/v2/payments");

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['count']), 'Missing count');
        $this->assertTrue(isset($content['page']), 'Missing page');
        $this->assertTrue(isset($content['limit']), 'Missing limit');
        $this->assertTrue(isset($content['total']), 'Missing total');
        $this->assertTrue(isset($content['items']), 'Missing items');

        foreach ($content['items'] as $item) {

            $this->assertTrue(isset($item['order']['id']), 'Missing item.order.id');
            $this->assertTrue(isset($item['status']), 'Missing status');
            $this->assertTrue(isset($item['id']), 'Missing id');
        }

    }

    /**
     * @small
     */
    public function test_put_success()
    {
        $client = $this->createAuthorizedAdmin();
        $container = $client->getContainer();

        $user = $this->createUser($container);

        $partner = $this->createPartner($container, CategoryType::JUNK_REMOVAL);


        $client = $this->createAuthorizedClient($user->getUsername());
        $container = $client->getContainer();
        $em = $container->get('doctrine')->getManager();
        $partner = $em->getRepository(Partner::class)->find($partner->getId());

        $order = $this->createOrder($container, $partner, $user);

        /** @var Payment $payment */
        $payment = $order->getPayments()->get(0);

        $payment->setStatus(PaymentStatus::CREATED);

        $em->persist($payment);
        $em->flush();

        $client = $this->createAuthorizedAdmin();

        $client->xmlHttpRequest('PUT', "/api/v2/payments/" . $payment->getId(), [], [], [], json_encode([
            'status' => PaymentStatus::SUCCESS
        ]));

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['status']), 'Missing status');
        $this->assertEquals(PaymentStatus::SUCCESS, $content['status'], 'Invalid status');

    }

    /**
     * @small
     */
    public function test_put_failure()
    {
        $client = $this->createAuthorizedAdmin();
        $container = $client->getContainer();

        $user = $this->createUser($container);

        $partner = $this->createPartner($container, CategoryType::JUNK_REMOVAL);


        $client = $this->createAuthorizedClient($user->getUsername());
        $container = $client->getContainer();
        $em = $container->get('doctrine')->getManager();
        $partner = $em->getRepository(Partner::class)->find($partner->getId());

        $order = $this->createOrder($container, $partner, $user);

        /** @var Payment $payment */
        $payment = $order->getPayments()->get(0);

        $payment->setStatus(PaymentStatus::CREATED);

        $em->persist($payment);
        $em->flush();

        $client = $this->createAuthorizedAdmin();

        $client->xmlHttpRequest('PUT', "/api/v2/payments/" . $payment->getId(), [], [], [], json_encode([
            'status' => PaymentStatus::FAILURE
        ]));

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['status']), 'Missing status');
        $this->assertEquals(PaymentStatus::FAILURE, $content['status'], 'Invalid status');

    }

    /**
     * @small
     */
    public function test_put_failed_payment_does_not_change_status()
    {
        $client = $this->createAuthorizedAdmin();
        $container = $client->getContainer();

        $user = $this->createUser($container);

        $partner = $this->createPartner($container, CategoryType::JUNK_REMOVAL);


        $client = $this->createAuthorizedClient($user->getUsername());
        $container = $client->getContainer();
        $em = $container->get('doctrine')->getManager();
        $partner = $em->getRepository(Partner::class)->find($partner->getId());

        $order = $this->createOrder($container, $partner, $user);

        /** @var Payment $payment */
        $payment = $order->getPayments()->get(0);

        $payment->setStatus(PaymentStatus::FAILURE);

        $em->persist($payment);
        $em->flush();

        $client = $this->createAuthorizedAdmin();

        $client->xmlHttpRequest('PUT', "/api/v2/payments/" . $payment->getId(), [], [], [], json_encode([
            'status' => PaymentStatus::SUCCESS
        ]));

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['status']), 'Missing status');
        $this->assertEquals(PaymentStatus::FAILURE, $content['status'], 'Invalid status');

    }

    /**
     * @small
     */
    public function test_put_success_payment_does_not_change_status()
    {
        $client = $this->createAuthorizedAdmin();
        $container = $client->getContainer();

        $user = $this->createUser($container);

        $partner = $this->createPartner($container, CategoryType::JUNK_REMOVAL);


        $client = $this->createAuthorizedClient($user->getUsername());
        $container = $client->getContainer();
        $em = $container->get('doctrine')->getManager();
        $partner = $em->getRepository(Partner::class)->find($partner->getId());

        $order = $this->createOrder($container, $partner, $user);

        /** @var Payment $payment */
        $payment = $order->getPayments()->get(0);

        $payment->setStatus(PaymentStatus::SUCCESS);

        $em->persist($payment);
        $em->flush();

        $client = $this->createAuthorizedAdmin();

        $client->xmlHttpRequest('PUT', "/api/v2/payments/" . $payment->getId(), [], [], [], json_encode([
            'status' => PaymentStatus::FAILURE
        ]));

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);

        $this->assertTrue(isset($content['id']), 'Missing id');
        $this->assertTrue(isset($content['status']), 'Missing status');
        $this->assertEquals(PaymentStatus::SUCCESS, $content['status'], 'Invalid status');

    }
}