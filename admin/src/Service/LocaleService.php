<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

class LocaleService
{
    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getSupportedLocales()
    {
        return explode('|',
            mb_strtolower($this->container->getParameter('supported_locales'), 'utf8')
        );
    }


}