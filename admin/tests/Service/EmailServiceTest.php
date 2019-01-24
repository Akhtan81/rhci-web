<?php

namespace App\Tests\Service;

use App\Entity\Partner;
use App\Entity\User;
use App\Service\EmailService;
use App\Tests\Classes\WebTestCase;

/**
 * @covers \App\Service\EmailService
 */
class EmailServiceTest extends WebTestCase
{
    /** @var EmailService */
    private $service;

    protected function setUp()
    {
        parent::setUp();
        $container = self::bootKernel()->getContainer();
        $this->service = $container->get(EmailService::class);
    }

    /**
     * @small
     */
    public function test_reset_password()
    {
        $user = new User();
        $user->setEmail(md5(uniqid()) . '@em.ail');
        $user->setName(md5(uniqid()));
        $user->setPhone(md5(uniqid()));

        $user->refreshPasswordToken();

        $this->service->sentResetPassword($user);

        $this->assertTrue(true);
    }

    /**
     * @small
     */
    public function test_on_partner_rejected()
    {
        $entity = new Partner();
        $entity->getUser()->setEmail(md5(uniqid()) . '@em.ail');

        $this->service->onPartnerRejected($entity);

        $this->assertTrue(true);
    }

    /**
     * @small
     */
    public function test_on_partner_approved()
    {
        $entity = new Partner();
        $entity->getUser()->setEmail(md5(uniqid()) . '@em.ail');

        $this->service->onPartnerApproved($entity);

        $this->assertTrue(true);
    }
}