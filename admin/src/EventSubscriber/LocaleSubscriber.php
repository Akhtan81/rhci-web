<?php

namespace App\EventSubscriber;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{
    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public static function getSubscribedEvents()
    {
        return [
            // must be registered before (i.e. with a higher priority than) the default Locale listener
            KernelEvents::REQUEST => ['onKernelRequest', 20],
        ];
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if ($request->headers->has('Accept-Language')) {
            $this->handleHeader($request);
        } elseif ($request->cookies->has('locale')) {
            $this->handleCookie($request);
        }
    }

    private function handleHeader(\Symfony\Component\HttpFoundation\Request $request)
    {
        $locales = explode(';', $request->headers->get('Accept-Language'));

        $defaultLocale = $this->container->getParameter('locale');
        $supportedLocales = explode('|', $this->container->getParameter('supported_locales'));

        $locale = null;
        foreach ($locales as $possibleLocale) {
            $possibleLocale = mb_strtolower(trim($possibleLocale), 'utf8');

            if (in_array($possibleLocale, $supportedLocales)) {
                $locale = $possibleLocale;
                break;
            }
        }

        if (!in_array($locale, $supportedLocales)) {
            $locale = $defaultLocale;
        }

        if ($request->hasSession()) {
            $request->getSession()->set('_locale', $locale);
        }

        $request->setLocale($locale);
    }

    private function handleCookie(\Symfony\Component\HttpFoundation\Request $request)
    {

        $locale = $request->cookies->get('locale');

        $defaultLocale = $this->container->getParameter('locale');
        $supportedLocales = explode('|', $this->container->getParameter('supported_locales'));

        if (!in_array($locale, $supportedLocales)) {
            $locale = $defaultLocale;
        }

        if ($request->hasSession()) {
            $request->getSession()->set('_locale', $locale);
        }

        $request->setLocale($locale);
    }
}