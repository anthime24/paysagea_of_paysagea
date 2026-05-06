<?php

namespace App\Front\Form;

use App\Core\Entity\BanquePhoto;
use App\Core\Entity\CreationType;
use App\Core\Entity\Ensoleillement;
use App\Core\Entity\Offre;
use App\Core\Entity\Projet;
use App\Core\Entity\Style;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class InscriptionProjectType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var Security
     */
    private $security;

    private $translator;

    public function __construct(EntityManagerInterface $em, Security $security, TranslatorInterface $translator)
    {
        $this->em = $em;
        $this->security = $security;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->security->getUser();
        $listPhotos = $this->em->getRepository(BanquePhoto::class)->findForInscription(
            $user !== null ? $user->getId() : null
        );

        $builder
            ->add(
                'adresse',
                TextType::class,
                array(
                    'label' => $this->translator->trans('Cherchons les plantes adaptées à votre adresse : *'),
                    'required' => true,
                    'constraints' => new NotBlank(),
                    'attr' => array('class' => 'ui-autocomplete-input form-input-address')
                )
            )
            ->add(
                'projetType',
                EntityType::class,
                array(
                    'class' => CreationType::class,
                    'data' => $this->em->getReference(CreationType::class, 1),
                    'label' => $this->translator->trans('Vous aménagez un(e) : *'),
                    'expanded' => true,
                    'multiple' => false,
                    'required' => true,
                    'constraints' => new NotBlank()
                )
            )
            ->add(
                'ensoleillement',
                EntityType::class,
                array(
                    'class' => Ensoleillement::class,
                    'data' => $this->em->getReference(Ensoleillement::class, 2),
                    'label' => 'Ensoleillement :',
                    'expanded' => true,
                    'multiple' => false,
                    'placeholder' => null
                )
            )
            ->add(
                'style',
                EntityType::class,
                array(
                    'label' => 'Style souhaité',
                    'required' => false,
                    'class' => Style::class,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('s')
                            ->orderBy('s.nom', 'ASC');
                    }
                )
            )
            ->add(
                'surface',
                TextType::class,
                array(
                    'label' => 'Surface (m²)',
                    'required' => false,
                    'attr' => array('class' => 'integer')
                )
            )
            ->add(
                'budgetMax',
                TextType::class,
                array(
                    'label' => 'Budget Max (€)',
                    'required' => false,
                    'attr' => array('class' => 'integer')
                )
            )
            ->add(
                'arrosageDeuxFoisParSemaine',
                CheckboxType::class,
                array(
                    'label' => 'Les plantes seront arrosées au moins deux fois par semaine en période sèche(manuellement ou automatiquement)',
                    'required' => false
                )
            )
            ->add('latitude', HiddenType::class, array())
            ->add('longitude', HiddenType::class, array())
            ->add('banquePhoto', InscriptionPhotoBankType::class, array('mapped' => false))
            ->add(
                'banquePhotos',
                EntityType::class,
                array(
                    'class' => BanquePhoto::class,
                    'choices' => $listPhotos,
                    'choice_label' => function($item) {
                        return $item->getLabel();
                    },
                    'choice_attr' => function ($object, $key, $value) {
                        return [
                            'data-photo-web-path' => $object->getWebPath(),
                            'data-banque-photo-type-id' => $object->getBanquePhotoType()->getId()
                        ];
                    },
                    'expanded' => true,
                    'multiple' => false,
                    'mapped' => false,
                    'required' => false,
                )
            )
            ->add(
                'offre',
                EntityType::class,
                array(
                    'class' => Offre::class,
                    'data' => $this->em->getReference(Offre::class, 1),
                    'label' => false,
                    'choice_label' => false,
                    'expanded' => true,
                    'multiple' => false,
                    'mapped' => false,
                    'required' => true,
                    'constraints' => new NotBlank()
                )
            )
//            ->add(
//                'recevoirInfosPartenaires',
//                CheckboxType::class,
//                array(
//                    'required' => false,
//                    'label' => 'J\'accepte de recevoir les informations concernant monjardin-materrasse.com et ses partenaires.'
//                )
//            )
//            ->add(
//                'cgu',
//                CheckboxType::class,
//                array(
//                    'mapped' => false,
//                    'required' => true,
//                    'label' => 'J\'ai lu et j\'accepte les Conditions Générales d\'Utilisation du site',
//                    'constraints' => new NotBlank()
//                )
//            )
            ->add(
                'creditPhotos',
                HiddenType::class,
                array(
                    'data' => $user != null ? $user->getCreditPhotos() : 0,
                    'mapped' => false
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => Projet::class
            )
        );
    }

    public function getBlockPrefix()
    {
        return 'mjmt_front_inscription_project';
    }

}
