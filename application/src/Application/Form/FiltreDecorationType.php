<?php

namespace App\Application\Form;

use App\Core\Entity\Categorie;
use App\Core\Entity\Couleur;
use App\Core\Entity\EntiteSousType;
use App\Core\Entity\Marque;
use App\Core\Entity\Matiere;
use App\Core\Entity\Style;
use App\Core\Repository\CategorieRepository;
use App\Core\Repository\EntiteSousTypeRepository;
use App\Core\Repository\MarqueRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class FiltreDecorationType extends AbstractType
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
                ->setParameter(':id', 4)
                ->groupBy('c')
                ->orderBy('c.nom');
        };

        $queryEntiteSousType = function (EntiteSousTypeRepository $est) {
            return $est->createQueryBuilder('est')
                ->leftJoin('est.entiteType', 'et')
                ->where('et.id = :id')
                ->setParameter(':id', 4)
                ->orWhere('est.entiteType is null')
                ->orderBy('est.nom');
        };

        $queryMarque = function (MarqueRepository $m) {
            return $m->createQueryBuilder('m')
                ->innerJoin('m.entites', 'e')
                ->leftJoin('e.entiteType', 'et')
                ->where('et.id = :id')
                ->andWhere('e.actif = 1')
                ->setParameter(':id', 4)
                ->groupBy('m')
                ->orderBy('m.nom');
        };

        $builder
            ->add(
                'nom',
                TextType::class,
                array('label' => ' ', 'attr' => array('placeholder' => $this->translator->trans('Nom')), 'required' => false)
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
                'entite_sous_type',
                EntityType::class,
                array(
                    'label' => ' ',
                    'placeholder' => $this->translator->trans('Type'),
                    'class' => EntiteSousType::class,
                    'query_builder' => $queryEntiteSousType,
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
                'matiere',
                EntityType::class,
                array(
                    'label' => ' ',
                    'placeholder' => $this->translator->trans('Matière'),
                    'class' => Matiere::class,
                    'required' => false,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('m')->orderBy('m.nom', 'ASC');
                    }
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
        return 'marque_blanche_appbundle_filtre_decoration';
    }

}
