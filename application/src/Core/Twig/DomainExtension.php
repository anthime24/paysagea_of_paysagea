<?php

namespace App\Core\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig\TwigFunction;
use Twig_Extension;
use Twig_SimpleFilter;

class DomainExtension extends Twig_Extension
{
    private $request;

    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack;
    }

    public function getName()
    {
        return "domain";
    }

    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter("domain_switch", array($this, "domainSwitch")),
        );
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction("is_app_domain", array($this, "isAppDomain"))
        );
    }

    private function getPrefixDomain()
    {
        $prefix = $this->request->getCurrentRequest()->getScheme() . '://';
        if (preg_match('/local\./', $this->request->getCurrentRequest()->getHttpHost()) > 0)
            $prefix .= 'local.';
        elseif (preg_match('/preprod\./', $this->request->getCurrentRequest()->getHttpHost()) > 0)
            $prefix .= 'preprod.';

        return $prefix;
    }

    public function domainSwitch($localeTarget)
    {
        $domain = '';
        switch ($localeTarget) {
            case 'fr':
                $domain = $this->getPrefixDomain() . 'app.monjardin-materrasse.com';
                break;
            case 'en':
                $domain = $this->getPrefixDomain() . 'app.virtualgardenplanner.com';
                break;
        }
        return $domain;
    }

    public function isAppDomain()
    {
        return $this->request->getCurrentRequest()->getHttpHost() == 'app.monjardin-materrasse.com' || $this->request->getCurrentRequest()->getHttpHost() == 'app.virtualgardenplanner.com';
    }
}
