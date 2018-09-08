<?php

namespace App\EventSubscriber;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LocaleSubscriber implements EventSubscriberInterface
{
    private $defaultLocale;

    public function __construct($defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;
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

        // try to see if the locale has been set as a locale cookie
        $locale = $request->cookies->get('locale');
        if (!$locale) {
            // if no explicit locale has been set on this request, use one from the session
            $locale = $request->getSession()->get('_locale', $this->defaultLocale);
        }

        $request->getSession()->set('_locale', $locale);
        $request->setLocale($locale);
    }
}