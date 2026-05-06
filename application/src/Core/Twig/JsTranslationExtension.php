<?php

namespace App\Core\Twig;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig_Extension;
use Twig_SimpleFilter;

class JsTranslationExtension extends Twig_Extension
{
    private $em;
    private $request = null;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getName()
    {
        return "translate_js";
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction("translate_js", array($this, "translateJs"))
        );
    }

    public function translateJs()
    {
        $translations = array();

        $query = $this->em->createQueryBuilder();
        $query->select("e.cle", "e.nom", "t.locale", "t.content")
            ->from(\App\Core\Entity\EnumerationJs::class, "e")
            ->leftJoin("e.translations", "t", "with", "t.field = 'nom'")
            ->orderBy("e.cle", "asc");

        $res = $query->getQuery()->getArrayResult();
        foreach($res as $item) {
            if(!isset($translations[$item['cle']])){
                $sanitizedValue = str_ireplace("'",  "&apos;", $item['nom']);
                $sanitizedValue = addslashes($sanitizedValue);

                $translations[$item['cle']] = array(
                    'fr' => $sanitizedValue
                );
            }

            if(isset($item['locale']) && $item['locale'] !== null && isset($item['content']) && $item['content'] !== null && trim($item['content']) != ""){
                $sanitizedValue = str_ireplace("'",  "&apos;", $item['content']);
                $sanitizedValue = addslashes($sanitizedValue);

                $translations[$item['cle']][$item['locale']] = $sanitizedValue;
            }
        }

        return $translations;
    }
}