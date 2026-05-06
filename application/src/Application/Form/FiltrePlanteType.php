<?php

namespace App\Application\Form;

use App\Core\Entity\BesoinEauGroupe;
use App\Core\Entity\Categorie;
use App\Core\Entity\Couleur;
use App\Core\Entity\Ensoleillement;
use App\Core\Entity\EntiteSousType;
use App\Core\Entity\Entretien;
use App\Core\Entity\Marque;
use App\Core\Entity\Rusticite;
use App\Core\Entity\TypeSol;
use App\Core\Repository\CategorieRepository;
use App\Core\Repository\EntiteSousTypeRepository;
use App\Core\Repository\MarqueRepository;
use App\Core\Repository\RusticiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class FiltrePlanteType extends AbstractType
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
        $queryCategories = function (CategorieRepository $er) {
            return $er->createQueryBuilder('c')
                ->leftJoin('c.entiteType', 'et')
                ->innerJoin('c.entites', 'e')
                ->where('et.id = :id or c.entiteType is null')
                ->andWhere('e.actif = 1')
                ->setParameter(':id', 1)
                ->groupBy('c')
                ->orderBy('c.nom');
        };

        $queryEntiteSousType = function (EntiteSousTypeRepository $est) {
            return $est->createQueryBuilder('est')
                ->leftJoin('est.entiteType', 'et')
                ->where('et.id = :id')
                ->setParameter(':id', 1)
                ->orWhere('est.entiteType is null')
                ->orderBy('est.nom');
        };

        $queryRusticite = function (RusticiteRepository $r) {
            return $r->createQueryBuilder('r')
                ->where('r.codeCouleur IS NOT NULL')
                ->andWhere('r.codeCouleur != \'\'')
                ->orderBy('r.min', 'ASC');
        };

        $queryMarque = function (MarqueRepository $m) {
            return $m->createQueryBuilder('m')
                ->innerJoin('m.entites', 'e')
                ->leftJoin('e.entiteType', 'et')
                ->where('et.id = :id')
                ->andWhere('e.actif = 1')
                ->setParameter(':id', 1)
                ->groupBy('m')
                ->orderBy('m.nom');
        };

        $optionsToxicite = array();
        $toxiciteOptionsQuery = $this->em->createQueryBuilder();
        $toxiciteOptionsQuery->select('t')
                            ->from(\App\Core\Entity\EnumerationAlimentaire::class, 't');

        foreach($toxiciteOptionsQuery->getQuery()->getResult() as $item) {
            $optionsToxicite[$item->getNom()] = $item->getCle();
        }

        $optionsCaduc = array();
        $caducOptionsQuery = $this->em->createQueryBuilder();
        $caducOptionsQuery->select('c')
            ->from(\App\Core\Entity\EnumerationCaduc::class, 'c');

        foreach($caducOptionsQuery->getQuery()->getResult() as $item) {
            $optionsCaduc[$item->getNom()] = $item->getCle();
        }

        $builder
            ->add(
                'nom',
                TextType::class,
                array('label' => ' ', 'required' => false, 'attr' => array('placeholder' => $this->translator->trans('Nom')))
            )
            ->add(
                'entite_sous_type_multiples',
                EntityType::class,
                array(
                    'attr' => array('placeholder' => $this->translator->trans('Sous Type'), 'class' => 'multipleSelectHolder'),
                    'multiple' => true,
                    'label' => ' ',
                    'placeholder' => $this->translator->trans('Type'),
                    'class' => EntiteSousType::class,
                    'query_builder' => $queryEntiteSousType,
                    'required' => false
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
                'rusticiteMultiples',
                EntityType::class,
                array(
                    'attr' => array('placeholder' => $this->translator->trans('Rusticité'), 'class' => 'multipleSelectHolder'),
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
                'toxique',
                ChoiceType::class,
                array(
                    'label' => ' ',
                    'placeholder' => $this->translator->trans('Alimentaire / Toxique'),
                    'choices' => $optionsToxicite,
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
                'caduc',
                ChoiceType::class,
                array(
                    'label' => ' ',
                    'placeholder' => $this->translator->trans('Caduc / Persistant'),
                    'choices' => $optionsCaduc,
                    'required' => false
                )
            )
            ->add(
                'marque',
                EntityType::class,
                array(
                    'label' => ' ',
                    'placeholder' => $this->translator->trans('Marque'),
                    'class' => Marque::class,
                    'required' => false,
                    'query_builder' => $queryMarque
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
            );
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
        return 'marque_blanche_appbundle_filtre_plante';
    }

}
