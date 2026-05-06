<?php

namespace App\Front\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ClientConnectionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                '_username',
                TextType::class,
                array('label' => 'Email *', 'required' => true, 'attr' => array('autocomplete' => 'off'))
            )
            ->add(
                '_password',
                PasswordType::class,
                array('label' => 'Mot de passe *', 'required' => true, 'attr' => array('autocomplete' => 'off'))
            )
            ->add('common', ClientConnectionCommonType::class);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('show_label' => false));
    }

}
