<?php

namespace App\Application\Form;

use App\Core\Entity\BesoinEauGroupe;
use App\Core\Entity\Categorie;
use App\Core\Entity\Couleur;
use App\Core\Entity\Ensoleillement;
use App\Core\Entity\Entretien;
use App\Core\Entity\Rusticite;
use App\Core\Entity\Style;
use App\Core\Entity\TypeSol;
use App\Core\Repository\CategorieRepository;
use App\Core\Repository\RusticiteRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class FiltreDecorType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $queryCategories = function (CategorieRepository $er) {
            return $er->createQueryBuilder('c')
                ->leftJoin('c.entiteType', 'et')
                ->innerJoin('c.entites', 'e')
                ->where('et.id = :id or c.entiteType is null')
                ->andWhere('e.actif = 1')
                ->setParameter(':id', 2)
                ->groupBy('c')
                ->orderBy('c.nom');
        };

        $queryRusticite = function (RusticiteRepository $r) {
            return $r->createQueryBuilder('r')
                ->where('r.codeCouleur IS NOT NULL')
                ->andWhere('r.codeCouleur != \'\'')
                ->orderBy('r.min', 'ASC');
        };

        $builder
            ->add(
                'nom',
                TextType::class,
                array('label' => ' ', 'required' => false, 'attr' => array('placeholder' => $this->translator->trans('Nom')))
            )
            ->add(
                'style',
                EntityType::class,
                array(
                    'label' => ' ',
                    'placeholder' => $this->translator->trans('Style'),
                    'class' => Style::class,
                    'required' => false,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('s')->orderBy('s.nom', 'ASC');
                    }
                )
            )
            ->add(
                'categories',
                EntityType::class,
                array(
                    'attr' => array('placeholder' => $this->translator->trans('Catégories'), 'class' => 'multipleSelectHolder'),
                    'multiple' => true,
                    'label' => ' ',
                    'placeholder' => $this->translator->trans('Catégorie'),
                    'class' => Categorie::class,
                    'query_builder' => $queryCategories,
                    'required' => false
                )
            )
            ->add(
                'couleur',
                EntityType::class,
                array(
                    'label' => ' ',
                    'placeholder' => $this->translator->trans('Couleur'),
                    'class' => Couleur::class,
                    'required' => false,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')->orderBy('c.nom', 'ASC');
                    }
                )
            )
            ->add(
                'entretien',
                EntityType::class,
                array('label' => ' ', 'placeholder' => $this->translator->trans('Entretien'), 'class' => Entretien::class, 'required' => false)
            )
            ->add(
                'ensoleillement',
                EntityType::class,
                array(
                    'label' => ' ',
                    'placeholder' => $this->translator->trans('Ensoleillement'),
                    'class' => Ensoleillement::class,
                    'required' => false
                )
            )
            ->add(
                'sol',
                EntityType::class,
                array(
                    'label' => ' ',
                    'placeholder' => $this->translator->trans('Type de sol'),
                    'class' => TypeSol::class,
                    'required' => false,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('s')->orderBy('s.nom', 'ASC');
                    }
                )
            )
            ->add(
                'rusticiteMultiples',
                EntityType::class,
                array(
                    'attr' => array('placeholder' => $this->translator->trans('Rusticités'), 'class' => 'multipleSelectHolder'),
                    'multiple' => true,
                    'label' => ' ',
                    'placeholder' => $this->translator->trans('Température minimale'),
                    'class' => Rusticite::class,
                    /*'choice_label'=>'min',*/
                    'query_builder' => $queryRusticite,
                    'required' => false
                )
            )
            ->add(
                'eauMultiples',
                EntityType::class,
                array(
                    'attr' => array('placeholder' => $this->translator->trans('Besoin en eau'), 'class' => 'multipleSelectHolder'),
                    'multiple' => true,
                    'label' => ' ',
                    'placeholder' => $this->translator->trans('Besoin en eau'),
                    'class' => BesoinEauGroupe::class,
                    'required' => false
                )
            )
            ->add(
                'arrosage',
                CheckboxType::class,
                array(
                    'label' => $this->translator->trans('Les plantes seront arrosées au moins deux fois par semaine en période sèche (manuellement ou automatiquement)'),
                    'required' => false
                )
            )
            ->add(
                'arrosageDeuxFoisParSemaineJardin',
                HiddenType::class,
                array(
                    'mapped' => false
                )
            )
            ->add(
                'arrosageDeuxFoisParSemaineTerrasse',
                HiddenType::class,
                array(
                    'mapped' => false
                )
            )/* Manque Designer */
        ;
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
        return 'marque_blanche_appbundle_filtre_decor';
    }

}
