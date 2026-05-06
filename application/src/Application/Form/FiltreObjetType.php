<?php

namespace App\Application\Form;

use App\Core\Entity\Categorie;
use App\Core\Entity\Couleur;
use App\Core\Entity\Style;
use App\Core\Repository\CategorieRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltreObjetType extends AbstractType
{
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

        $builder
            ->add('nom', TextType::class, array('label' => 'Nom', 'required' => false))
            ->add(
                'couleur',
                EntityType::class,
                array('label' => 'Couleur', 'class' => Couleur::class, 'required' => false)
            )
            ->add(
                'categorie',
                EntityType::class,
                array(
                    'label' => 'Catégorie',
                    'class' => Categorie::class,
                    'query_builder' => $queryCategories,
                    'required' => false
                )
            )
            ->add('style', EntityType::class, array('label' => 'Style', 'class' => Style::class, 'required' => false));
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
        return 'marque_blanche_appbundle_filtre_objet';
    }

}
