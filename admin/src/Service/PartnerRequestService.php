<?php

namespace App\Service;

use App\Entity\PartnerRequest;
use JMS\Serializer\SerializationContext;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PartnerRequestService
{

    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function serialize($content, $locale, $groups = [])
    {
        $groups[] = 'api_v1';

        $result = json_decode($this->container->get('jms_serializer')
            ->serialize($content, 'json', SerializationContext::create()
                ->setGroups($groups)), true);

        if ($content instanceof PartnerRequest) {
            $this->onPostSerialize($result, $locale);
        } else {
            foreach ($result as &$item) {
                $this->onPostSerialize($item, $locale);
            }
        }

        return $result;
    }

    public function onPostSerialize(&$content, $locale)
    {
        unset($content['partner']);
    }


}