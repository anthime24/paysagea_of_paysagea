<?php

namespace App\Core\Listener;

use Symfony\Component\HttpKernel\Event\RequestEvent;

class LocaleListener {

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $host = $request->getHost();

        if (stripos($host, 'virtualgardenplanner.com') !== false) {
            $request->setLocale('en');
            $request->getSession()->set('_locale', 'en');
        } else {
            $request->setLocale('fr');
            $request->getSession()->set('_locale', 'fr');
        }
    }
}
