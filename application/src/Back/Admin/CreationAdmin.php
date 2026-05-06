<?php

namespace App\Back\Admin;

use App\Core\Entity\CreationType;
use App\Core\Entity\Ensoleillement;
use App\Core\Entity\Style;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CreationAdmin extends AbstractAdmin
{
    /**
     * {@inheritdoc}
     */
    protected $datagridValues = array(
        'confirmer' => array('value' => true),
        '_sort_order' => 'DESC',
        '_sort_by' => 'dateModification'
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
            array('_controller' => 'App\Back\Controller\CreationAdminController::application')
        );
        $collection->add(
            'duplicate',
            'duplicate',
            array('_controller' => 'App\Back\Controller\CreationAdminController::duplicate')
        );
        $collection->add('pdf', 'pdf', array('_controller' => 'App\Back\Controller\CreationAdminController::pdf'));
        $collection->add(
            'exportEntite',
            'exportEntite',
            array('_controller' => 'App\Back\Controller\CreationAdminController::exportEntite')
        );
        $collection->add(
            'exportDevis',
            'exportDevis',
            array('_controller' => 'App\Back\Controller\CreationAdminController::exportDevis')
        );
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id', null, array('label' => 'Identifiant de la création', 'show_filter' => true))
            ->add('nom')
            ->add('creationType', null, array('label' => 'Type de création'))
            ->add('projet.codePostal', null, array('label' => 'Code postal'))
            ->add('projet.client', null, array('label' => 'Client', 'show_filter' => true))
            ->add('style')
            ->add('confirmer');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id', null, array('sortable' => true, 'label' => 'Identifiant de la création'))
            ->add('custom', 'string', array('template' => 'back/creation/list_custom.html.twig', 'label' => 'Nom'))
            ->add('projet.client', null, array('label' => 'Client'))
            ->add('surface')
            ->add('style.nom', null, array('sortable' => true, 'label' => 'Style'))
            ->add('dateModification', null, array('sortable' => true, 'label' => 'Date dernière modification'))
            ->add('projet.codePostal', null, array('label' => 'Code postal'))
            ->add('confirmer')
            ->add(
                '_action',
                'actions',
                array(
                    'actions' => array(
                        'application' => array('template' => 'back/creation/list__action_application.html.twig'),
                        'duplicate' => array('template' => 'back/creation/list__action_duplicate.html.twig'),
                        'edit' => array(),
                        'delete' => array(),
                        'pdf' => array('template' => 'back/creation/list__action_pdf.html.twig'),
                        'exportEntite' => array('template' => 'back/creation/list__action_export_entite.html.twig'),
                        'exportDevis' => array('template' => 'back/creation/list__action_export_devis.html.twig'),
                    )
                )
            );
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $queryStyles = $this->modelManager->getEntityManager(Style::class)->getRepository(
            Style::class
        )->findAllOrderedByName();
        $queryEnsoleillements = $this->modelManager->getEntityManager(Ensoleillement::class)->getRepository(
            Ensoleillement::class
        )->findAllOrderedByName();

        $formMapper
            ->with('Création')
            ->add('nom')
            ->add('dateCreation', null, array('disabled' => true))
            ->add('surface')
            ->add(
                'ensoleillement',
                ModelType::class,
                array('multiple' => false, 'expanded' => false, 'required' => false, 'query' => $queryEnsoleillements)
            )
            ->add(
                'creationType',
                EntityType::class,
                array(
                    'class' => CreationType::class,
//                'property' => 'nom',
                    'expanded' => false,
                    'multiple' => false,
                    'label' => 'Type de projet'
                )
            )
            ->add(
                'style',
                ModelType::class,
                array('multiple' => false, 'expanded' => false, 'required' => false, 'query' => $queryStyles)
            )
            ->add('referenceLecture', null, array('disabled' => true))
            ->add('referenceEcriture', null, array('disabled' => true))
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    public function postUpdate($object)
    {
        $object->moveUpload();
    }

    /**
     * {@inheritdoc}
     */
    public function postPersist($object)
    {
        $object->moveUpload();
    }

    /**
     * {@inheritdoc}
     */
    public function preRemove($object)
    {
        $object->removeUpload();
    }

}
