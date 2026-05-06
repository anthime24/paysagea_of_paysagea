<?php

namespace App\Back\Admin;

use App\Core\Entity\Creation;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class CreationEntiteAdmin extends AbstractAdmin
{
    /**
     * {@inheritdoc}
     */
    protected $parentAssociationMapping = 'entite';


    /**
     * {@inheritdoc}
     */
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'creation'
    );

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);

        $collection->add(
            'application',
            'application',
            array('_controller' => 'App\Back\Controller\CreationEntiteAdminController::application')
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('creation.nom')
            ->add('creation.creationType', null, array('label' => 'Type de création'))
            ->add('creation.projet.client', null, array('label' => 'Client'))
            ->add('creation.style')
            ->add('creation.confirmer');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add(
                'custom',
                'string',
                array('template' => 'back/creation_entite/list_custom.html.twig', 'label' => 'Rendu')
            )
            ->add(
                'creation',
                'sonata_type_model',
                array(
                    'class' => Creation::class,
                    'property' => '',
                    'sortable' => false
                )
            )
            ->add('creation.projet.client', null, array('sortable' => true, 'label' => 'Client'))
            ->add('creation.surface', null, array('label' => 'Surface'))
            ->add('creation.style.nom', null, array('sortable' => true, 'label' => 'Style'))
            ->add('creation.dateCreation', null, array('sortable' => true, 'label' => 'Date Création'))
            ->add('creation.confirmer', null, array('sortable' => true, 'label' => 'Confirmer'))
            ->add(
                '_action',
                'actions',
                array(
                    'actions' => array(
                        'application' => array('template' => 'back/creation/list__action_application.html.twig'),
                    )
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        /*$formMapper
            ->add('date_utilisation')
        ;*/
    }
}
