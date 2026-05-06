<?php

namespace App\Back\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ListType extends AbstractType
{
    public function setDefaultSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'template' => 'BackBundle:list.html.twig'
            )
        );
    }

    public function getParent()
    {
        return 'entity';
    }

    public function getBlockPrefix()
    {
        return 'mjmt_list_type';
    }
}
