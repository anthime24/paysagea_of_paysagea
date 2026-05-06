<?php

namespace App\Front\Form;

use App\Core\Entity\Client;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ClientEditType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'nom',
                TextType::class,
                array(
                    'required' => true,
                    'label' => 'Nom *',
                    'attr' => array('autocomplete' => 'off'),
                    'constraints' => new NotBlank(array('message' => 'Ce champ est obligatoire'))
                )
            )
            ->add(
                'prenom',
                TextType::class,
                array(
                    'required' => true,
                    'label' => 'Prénom *',
                    'attr' => array('autocomplete' => 'off'),
                    'constraints' => new NotBlank(array('message' => 'Ce champ est obligatoire'))
                )
            )
            ->add(
                'adresse',
                TextType::class,
                array(
                    'required' => false,
                    'label' => 'Adresse de facturation',
                    'attr' => array('autocomplete' => 'off')
                )
            )
            ->add(
                'email',
                EmailType::class,
                array(
                    'required' => true,
                    'label' => 'E-mail *',
                    'attr' => array('autocomplete' => 'off'),
                    'constraints' => array(
                        new Email(array('message' => 'Email non valide')),
                        new NotBlank(array('message' => 'Ce champ est obligatoire'))
                    )
                )
            )
            ->add(
                'telephone',
                TextType::class,
                array(
                    'required' => false,
                    'label' => 'Téléphone',
                    'attr' => array(
                        'maxlength' => '10',
                        'minlength' => '10',
                        'autocomplete' => 'off'
                    ),
                    'constraints' => array(
                        new Length(array('max' => 10, 'min' => 10, 'maxMessage' => 'Ce champ doit contenir maximum 10 caractères', 'minMessage' => 'Ce champ doit contenir minimum 10 caractères')),
                    )
                )
            )
            ->add(
                'passwordRepeat',
                RepeatedType::class,
                array(
                    'type' => PasswordType::class,
                    'invalid_message' => 'Les mots de passe doivent être identiques',
                    'required' => false,
                    'mapped' => false,
                    'first_options' => array('label' => 'Nouveau mot de passe', 'attr' => array('autocomplete' => 'off')),
                    'second_options' => array(
                        'label' => 'Nouveau mot de passe (confirmation)',
                        'attr' => array('autocomplete' => 'off')
                    )
                )
            )
            ->add('recevoirInformations',
                ChoiceType::class,
                [
                    'label' => 'J\'accepte de recevoir des informations concernant monjardin-materrasse.com et ses partenaires ?',
                    'choices' => [
                        'Oui' => 1,
                        'Non' => 0,
                    ],
                    'expanded' => true
                ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => Client::class
            )
        );
    }

}
