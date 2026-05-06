<?php

namespace App\Front\Form;

use App\Core\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class InscriptionClientType extends AbstractType
{

    private $em;
    private $translator;

    public function __construct(EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {
        $this->em = $entityManager;
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $optionsTypePersonne = array();
        $optionsTypePersonneQuery = $this->em->createQueryBuilder();
        $optionsTypePersonneQuery->select('t')
            ->from(\App\Core\Entity\EnumerationTypePersonne::class, 't');

        foreach ($optionsTypePersonneQuery->getQuery()->getResult() as $item) {
            $optionsTypePersonne[$item->getNom()] = $item->getCle();
        }

        $builder
            ->add(
                'typePersonne',
                ChoiceType::class,
                array(
                    'required' => true,
                    'label' => 'Je suis *',
                    'choices' => $optionsTypePersonne,
                    'multiple' => false,
                    'expanded' => true
                )
            )
            ->add(
                'nom',
                TextType::class,
                array(
                    'required' => true,
                    'label' => 'Nom *',
                    'attr' => array('autocomplete' => 'off'),
                    'constraints' => new NotBlank()
                )
            )
            ->add(
                'prenom',
                TextType::class,
                array(
                    'required' => true,
                    'label' => 'Prénom *',
                    'attr' => array('autocomplete' => 'off'),
                    'constraints' => new NotBlank()
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
                        new Email(),
                        new NotBlank()
                    )
                )
            )
            ->add(
                'password',
                RepeatedType::class,
                array(
                    'type' => PasswordType::class,
                    'invalid_message' => 'Les mots de passe doivent être identiques',
                    'options' => array('required' => true),
                    'constraints' => new NotBlank(),
                    'first_options' => array('label' => 'Mot de passe *', 'attr' => array('autocomplete' => 'off')),
                    'second_options' => array(
                        'label' => 'Mot de passe (confirmation) *',
                        'attr' => array('autocomplete' => 'off')
                    )
                )
            )
            ->add(
                'cgu',
                CheckboxType::class,
                array(
                    'mapped' => false,
                    'required' => true,
                    'label' => 'J\'ai lu et j\'accepte les Conditions Générales d\'Utilisation du site *',
                    'constraints' => new NotBlank()
                )
            )
            ->add(
                'recevoirInformations',
                CheckboxType::class,
                array(
                    'label' => 'J\'accepte de recevoir des informations concernant monjardin-materrasse.com et ses partenaires',
                    'required' => false
                )
            )
            ->add('common', ClientConnectionCommonType::class, [
                'mapped' => false
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
