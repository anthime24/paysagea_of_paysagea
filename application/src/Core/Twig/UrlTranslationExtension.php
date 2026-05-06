<?php

namespace App\Core\Twig;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig_Extension;
use Twig_SimpleFilter;

class UrlTranslationExtension extends Twig_Extension
{
    private $em;
    private $request = null;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getName()
    {
        return "translate_url";
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction("translate_url", array($this, "translateUrl"))
        );
    }

    public function translateUrl(Request $request, $slug, $absoluteUrl = false)
    {
        $url = '';
        $locale = $request->getLocale();

        $query = $this->em->createQueryBuilder();
        $cmsEntity = $query->select('c')
            ->from(\App\Core\Entity\Cms::class, 'c')
            ->where('c.slug = :slug')
            ->setParameter(':slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();

        if($locale == 'fr') {
            if($cmsEntity !== null && $cmsEntity->getUrl() !== null && trim($cmsEntity->getUrl()) != "") {
                $url = trim($cmsEntity->getUrl());
            }
        } else if($cmsEntity !== null) {
            $queryTranslation = $this->em->createQueryBuilder();
            $translatedEntity = $queryTranslation->select('ct')
                ->from(\App\Core\Entity\Translation\Cms::class, 'ct')
                ->where('IDENTITY(ct.object) = :translatedObjectId')
                ->setParameter(':translatedObjectId', $cmsEntity->getId())
                ->andWhere('ct.locale = :locale')
                ->setParameter(':locale', $locale)
                ->andWhere('ct.field = :translatedField')
                ->setParameter(':translatedField', 'url')
                ->getQuery()
                ->getOneOrNullResult();

            if($translatedEntity !== null && $translatedEntity->getField() !== null && trim($translatedEntity->getField() != "")) {
                $url = trim($translatedEntity->getContent());
            } else {
                $url = $cmsEntity->getUrl();
            }
        }

        if($absoluteUrl === false) {
            return '/' . $url;
        } else {
            return $request->getSchemeAndHttpHost() . '/' . $url;
        }
    }
}
