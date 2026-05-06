<?php

namespace App\Core\Twig;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig_Extension;
use Twig_SimpleFilter;

class EnumTranslationExtension extends Twig_Extension
{
    private $em;
    private $request = null;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $em)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->em = $em;
    }

    public function getName()
    {
        return "translate_enum";
    }

    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter("translate_enum", array($this, "translateEnum")),
        );
    }

    public function translateEnum($enumValue, $enumClass)
    {
        $locale = $this->request->getLocale();
        $query = $this->em->createQueryBuilder();
        $query->select("e.nom", "t.content")
              ->from($enumClass, "e")
              ->leftJoin("e.translations", "t", "with", "t.field = 'nom' and t.locale = :locale")
              ->where('LOWER(e.nom) = :nom')
              ->setParameter(':nom', strtolower($enumValue))
              ->setParameter(':locale', $locale);

        $res = $query->getQuery()->getArrayResult();

        if(count($res) < 1) {
            $res = array();
        } else {
            $res = $res[0];
        }

        if(!isset($res['content']) || trim($res['content']) == "") {
            return $enumValue;
        } else {
            return $res["content"];
        }
    }
}