<?php

namespace App\Back\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImportType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'desactiver_entite_non_presente',
                CheckboxType::class,
                array('label' => 'Désactiver les entités non présente dans le fichier', 'required' => false)
            )
            ->add(
                'reset_nouveau',
                CheckboxType::class,
                array(
                    'label' => 'Remise à zéro du champ "nouveau"',
                    'attr' => array('checked' => 'checked'),
                    'required' => false
                )
            )
            ->add('fichier', FileType::class, array('label' => 'Fichier CSV (séparateur ;)', 'required' => true));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array());
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'mjmt_back_import';
    }
}
