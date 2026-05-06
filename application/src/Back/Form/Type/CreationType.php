<?php

namespace App\Back\Form\Type;

use App\Core\Entity\Client;
use App\Core\Entity\Creation;
use App\Core\Entity\Ensoleillement;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class CreationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'client',
                EntityType::class,
                array(
                    'class' => Client::class,
                    'label' => 'Client',
                    'required' => false,
                    'choice_label' => 'toFullString',
                    'mapped' => false,
                    'expanded' => false,
                    'multiple' => false,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('u')
                            ->orderBy('u.nom', 'ASC');
                    },
                    'empty_value' => '--',
                    'attr' => array(
                        'class' => 'select2',
                        'data-sonata-select2' => 'true'
                    )
                )
            )
            ->add(
                'client_email',
                TextType::class,
                array(
                    'label' => 'Adresse e-mail du client',
                    'required' => false,
                    'constraints' => array(
                        new Email(array('message' => 'L\'e-mail enseigné est invalide.')),
                    ),
                    'mapped' => false,
                )
            )
            ->add(
                'creation_type',
                EntityType::class,
                array(
                    'class' => \App\Core\Entity\CreationType::class,
                    'label' => 'Mon espace à aménager',
                    'required' => true,
                    'expanded' => true,
                    'multiple' => false,
                    'attr' => array('class' => 'radio-block')
                )
            )
            ->add(
                'ensoleillement',
                EntityType::class,
                array(
                    'class' => Ensoleillement::class,
                    'label' => 'Ensoleillement',
                    'required' => true,
                    'expanded' => true,
                    'multiple' => false,
                    'attr' => array('class' => 'radio-block')
                )
            )
            ->add(
                'photo',
                FileType::class,
                array(
                    'label' => 'Photo de mon espace à aménager (de préférence en format paysage, JPEG, max 10Mo)',
                    'mapped' => false,
                    'required' => false,
                    'attr' => array(
                        'class' => 'input-file-button',
                    ),
                    'constraints' => new Callback(array($this, 'validatePhoto'))
                )
            )
            ->add('nom', TextType::class, array('label' => 'Nom de ma photo', 'required' => true))
            ->add(
                'idPhotoSelectionnee',
                HiddenType::class,
                array('label' => false, 'required' => false, 'mapped' => false)
            )
            ->add(
                'submit',
                SubmitType::class,
                array(
                    'label' => "Créer et retourner à la liste",
                    'attr' => array('class' => 'button button-botanic button-success')
                )
            )
            ->add(
                'nom_projet',
                TextType::class,
                array('label' => 'Nom de mon projet', 'required' => true, 'mapped' => false)
            )
            ->add(
                'code_postal',
                TextType::class,
                array(
                    'label' => "Localisation de mon projet",
                    'required' => true,
                    'mapped' => false,
                    'attr' => array('size' => '5', 'placeholder' => $this->translator->trans('Code postal'))
                )
            );
    }

    public function validatePhoto($value, ExecutionContextInterface $context)
    {
        /** @var Form $form */
        $form = $context->getRoot();
        $data = $form->getData();

        if (null === $form['photo']->getData() && (null === $form['idPhotoSelectionnee']->getData(
                ) || '' == $form['idPhotoSelectionnee']->getData())) {
            $context->buildViolation(
                'Attention ! Vous devez sélectionner une image (à importer ou à choisir ci-dessous) pour continuer.'
            )
                ->addViolation();
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => Creation::class,
                'projetIsNew' => false
            )
        );
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'mjmtappbundle_creation';
    }

}
