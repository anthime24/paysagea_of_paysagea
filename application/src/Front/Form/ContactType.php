<?php

namespace App\Front\Form;

use Gregwar\CaptchaBundle\Type\CaptchaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, array('label' => 'Nom *', 'required' => true))
            ->add('email', TextType::class, array('label' => 'E-mail *', 'required' => true))
            ->add('site_web', TextType::class, array('label' => 'Site Web', 'required' => false))
            ->add('commentaire', TextareaType::class, array('label' => 'Commentaire *', 'required' => true))
            ->add(
                'captcha',
                CaptchaType::class,
                array('label' => 'Contrôle anti-robot (merci de recopier le texte de l\'image)')
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $collectionConstraint = new Collection(
            array(
                'nom' => array(
                    new NotBlank()
                ),
                'email' => array(
                    new NotBlank(),
                    new Email()
                ),
                'site_web' => array(),
                'commentaire' => array(
                    new NotBlank()
                )
            )
        );

        $resolver->setDefaults(
            array(
                'constraints' => $collectionConstraint
            )
        );
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'mjmt_front_contact';
    }

}
