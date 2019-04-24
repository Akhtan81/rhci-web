<?php

namespace App\Service;

use App\Entity\RequestedCategory;
use JMS\Serializer\SerializationContext;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RequestedCategoryService
{

    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function serializeV2($content, $locale)
    {
        return $this->serialize($content, $locale, ['api_v2']);
    }


    public function serialize($content, $locale, $groups = [])
    {
        $groups[] = 'api_v1';

        $result = json_decode($this->container->get('jms_serializer')
            ->serialize($content, 'json', SerializationContext::create()
                ->setGroups($groups)), true);

        if ($content instanceof RequestedCategory) {
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
        $categoryService = $this->container->get(CategoryService::class);

        unset($content['partner']);

        if(isset($content['category'])) {
            $categoryService->onPostSerialize($content['category'], $locale);
        }
    }


}