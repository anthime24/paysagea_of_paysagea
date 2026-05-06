<?php

namespace App\Back\Form\Type;

use Sonata\CoreBundle\Form\Type\DatePickerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PictogrammeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pictoNouveau', null, array('required' => false, 'label' => 'Nouveau'))
            ->add(
                'dateDebutPictoNouveau',
                DatePickerType::class,
                array('required' => false, 'label' => 'Date de debut', 'format' => 'dd/MM/yyyy')
            )
            ->add(
                'dateFinPictoNouveau',
                DatePickerType::class,
                array('required' => false, 'label' => 'Date de fin', 'format' => 'dd/MM/yyyy')
            )
            ->add('pictoPromo', null, array('required' => false, 'label' => 'Promotion'))
            ->add(
                'dateDebutPictoPromo',
                DatePickerType::class,
                array('required' => false, 'label' => 'Date de debut', 'format' => 'dd/MM/yyyy')
            )
            ->add(
                'dateFinPictoPromo',
                DatePickerType::class,
                array('required' => false, 'label' => 'Date de fin', 'format' => 'dd/MM/yyyy')
            )
            ->add('pictoCoupCoeur', null, array('required' => false, 'label' => 'Coup de coeur'))
            ->add(
                'dateDebutPictoCoupCoeur',
                DatePickerType::class,
                array('required' => false, 'label' => 'Date de debut', 'format' => 'dd/MM/yyyy')
            )
            ->add(
                'dateFinPictoCoupCoeur',
                DatePickerType::class,
                array('required' => false, 'label' => 'Date de fin', 'format' => 'dd/MM/yyyy')
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'inherit_data' => true,
            ]
        );
    }

    public function getBlockPrefix()
    {
        return 'mjmt_pictogramme_type';
    }
}
