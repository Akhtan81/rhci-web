<?php

namespace App\Tests\Controller;

use App\Tests\Classes\WebTestCase;

/**
 * @covers \App\Controller\IndexController
 */
class IndexControllerTest extends WebTestCase
{

    public function testIndexPageOpens()
    {
        $client = $this->createUnauthorizedClient();

        $client->request('GET', "/");

        $response = $client->getResponse();

        $this->assertTrue($response->isSuccessful());
    }

    public function testIndexRedirectsToMyCourses()
    {
        $client = $this->createAuthorizedClient();

        $client->request('GET', "/");

        $response = $client->getResponse();

        $this->assertTrue($this->isRedirect($response, '/courses/my'));
    }

    /**
     * @group ignore
     */
    public function testPrivacyPageOpens()
    {
        $client = $this->createUnauthorizedClient();

        $client->request('GET', "/privacy");

        $response = $client->getResponse();

        $this->assertTrue($response->isSuccessful());
    }

    /**
     * @group ignore
     */
    public function testTermsPageOpens()
    {
        $client = $this->createUnauthorizedClient();

        $client->request('GET', "/terms");

        $response = $client->getResponse();

        $this->assertTrue($response->isSuccessful());
    }

    /**
     * @group ignore
     */
    public function testHelpPageOpens()
    {
        $client = $this->createUnauthorizedClient();

        $client->request('GET', "/help");

        $response = $client->getResponse();

        $this->assertTrue($response->isSuccessful());
    }
}