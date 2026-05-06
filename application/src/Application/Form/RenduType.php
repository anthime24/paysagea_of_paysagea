<?php

namespace App\Application\Form;

use App\Core\Entity\Annee;
use App\Core\Entity\Mois;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RenduType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('annee', EntityType::class, array('label' => 'Année', 'class' => Annee::class))
            ->add('mois', EntityType::class, array('label' => 'Mois de floraison', 'class' => Mois::class));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('csrf_protection' => false));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'marque_blanche_appbundle_rendu';
    }

}
