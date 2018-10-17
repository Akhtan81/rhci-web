<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

class PushService
{

    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param $orderId
     * @param $userId
     * @return int
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendPickupInProgress($orderId, $userId)
    {
        $isEnabled = $this->container->getParameter('one_signal_push_enabled');
        if (!$isEnabled) return -1;

        $client = $this->createClient();

        $appId = $this->container->getParameter('one_signal_app_id');
        $trans = $this->container->get('translator');

        $res = $client->request('POST', 'https://onesignal.com/api/v1/notifications', [
            \GuzzleHttp\RequestOptions::JSON => [
                "app_id" => $appId,
                "filters" => [
                    [
                        "field" => "tag",
                        "key" => "order_id",
                        "relation" => "=",
                        "value" => $orderId
                    ],
                    [
                        "field" => "tag",
                        "key" => "user_id",
                        "relation" => "=",
                        "value" => $userId
                    ],
                ],
                "contents" => [
                    "en" => $trans->trans('push.pickup_in_progress', [], null, 'en'),
                ]
            ]
        ]);

        return $res->getStatusCode();
    }

    private function createClient()
    {
        $apiKey = $this->container->getParameter('one_signal_api_key');

        $client = new \GuzzleHttp\Client([
            'headers' => [
                'Authorization' => 'Basic ' . $apiKey
            ]
        ]);

        return $client;
    }
}