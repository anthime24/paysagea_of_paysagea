<?php

namespace App\Front\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

class InscriptionCropType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rotation', HiddenType::class, array())
            ->add('x1', HiddenType::class, array())
            ->add('y1', HiddenType::class, array())
            ->add('x2', HiddenType::class, array())
            ->add('y2', HiddenType::class, array())
            ->add('width', HiddenType::class, array())
            ->add('height', HiddenType::class, array())
            ->add('originalWidth', HiddenType::class, array())
            ->add('originalHeight', HiddenType::class, array());
    }
}