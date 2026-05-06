<?php

namespace App\Back\Admin;

use App\Back\Form\Type\TranslatedType;
use App\Core\Entity\Categorie;
use App\Core\Entity\Rusticite;
use App\Core\Entity\Style;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CompositionAdmin extends AbstractAdmin
{
    /**
     * {@inheritdoc}
     */
    protected $datagridValues = array(
        '_sort_order' => 'ASC',
        '_sort_by' => 'nom'
    );

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('nom');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        //$collection->clearExcept(array('list', 'new', 'edit'));
        $collection->add(
            'rechercheEntites',
            'recherche-entites',
            array('_controller' => 'App\Back\Controller\CompositionAdminController::rechercheEntites')
        );
        $collection->add(
            'visualiser',
            'visualiser/{id}',
            array('_controller' => 'App\Back\Controller\CompositionAdminController::visualiser'),
            array('id' => '\d+')
        );
        $collection->add(
            'duplicate',
            'duplicate',
            array('_controller' => 'App\Back\Controller\CompositionAdminController::duplicate')
        );
        //$collection->add('export', 'export');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('nom')
            ->add('nbCasesX')
            ->add('nbCasesY')
            ->add(
                '_action',
                'actions',
                array(
                    'actions' => array(
                        'visualiser' => array('template' => 'back/composition/list__action_visualiser.html.twig'),
                        'duplicate' => array('template' => 'back/composition/list__action_duplicate.html.twig'),
                        'edit' => array(),
                        'delete' => array(),
                    )
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $queryCategories = $this->modelManager->getEntityManager(Categorie::class)->getRepository(
            Categorie::class
        )->findAllOrderedByName();
        $queryStyles = $this->modelManager->getEntityManager(Style::class)->getRepository(
            Style::class
        )->findAllOrderedByName();
        $queryRusticites = $this->modelManager->getEntityManager(Rusticite::class)->getRepository(
            Rusticite::class
        )->findAllOrderedByName();

        $formMapper
            ->tab('Informations')
            ->with('')
            ->add('nom', TranslatedType::class, array(
                'propertyName' => 'nom'
            ))
            ->add('nbCasesX')
            ->add('nbCasesY')
            ->add('espacementObjet', null, array('label' => 'Espacement Objet (cm)', 'required' => false))
            ->add(
                'hauteurFuite',
                ChoiceType::class,
                array(
                    'choices' => array(
                        '1.2' => '1.2',
                        '1.3' => '1.3',
                        '1.4' => '1.4',
                        '1.5' => '1.5',
                        '1.6' => '1.6',
                        '1.7' => '1.7',
                        '1.8' => '1.8',
                        '1.9' => '1.9',
                        '2' => '2'
                    ),
                    'required' => true,
                    'help' => '<br/><br/>&uarr; Plus la valeur est élevée, plus le point de fuite sera loin'
                )
            )
            ->add('gratuit')
            ->add('creationTypes', null, array('label' => 'Types Création', 'required' => false))
            ->add('entretien', null, array())
            ->add('typeSols', ModelType::class, array('multiple' => true, 'expanded' => true, 'required' => false))
            ->add(
                'ensoleillements',
                ModelType::class,
                array('multiple' => true, 'expanded' => true, 'required' => false)
            )
            ->add('besoinEauMultiples', null, array('label' => 'Besoin Eau', 'multiple' => true))
            ->add(
                'rusticiteMultiples',
                ModelType::class,
                array(
                    'label' => 'Rusticite',
                    'multiple' => true,
                    'query' => $queryRusticites
                )
            )
            ->end()
            ->end()
            ->tab('Categories')
            ->with('')
            ->add(
                'categories',
                ModelType::class,
                array('multiple' => true, 'expanded' => true, 'required' => false, 'query' => $queryCategories)
            )
            ->end()
            ->end()
            ->tab('Styles')
            ->with('')
            ->add(
                'styles',
                ModelType::class,
                array('multiple' => true, 'expanded' => true, 'required' => false, 'query' => $queryStyles)
            )
            ->end()
            ->end();

        $this->setTemplate('edit', 'back/composition/base_edit.html.twig');
    }

    public function getFormTheme()
    {
        return array_merge(
            parent::getFormTheme(),
            array(
                'back/form/translatedField.html.twig'
            )
        );
    }
}
