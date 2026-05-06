<?php

namespace App\Back\Admin;

use App\Core\Entity\Client;
use App\Core\Entity\Creation;
use App\Core\Entity\Ensoleillement;
use App\Core\Entity\Ph;
use App\Core\Entity\Precipitation;
use App\Core\Entity\Rusticite;
use App\Core\Entity\Style;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ProjetAdmin extends AbstractAdmin
{
    /**
     * {@inheritdoc}
     */
    protected $datagridValues = array(
        'confirmer' => array('value' => true),
        '_sort_order' => 'DESC',
        '_sort_by' => 'dateCreation'
    );

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('nom')
            ->add('projetType', null, array('label' => 'Type de projet'))
            ->add('client')
            ->add('confirmer');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('nom')
            ->add(
                'client',
                'sonata_type_model',
                array(
                    'class' => Client::class,
                    'property' => 'nom'
                )
            )
            ->add('dateCreation')
            ->add('projetType.nom', null, array('label' => 'Type de projet', 'sortable' => true))
            ->add('budgetMax', 'float', array('template' => 'MjmtBackBundle:Projet:list_budget_max_custom.html.twig'))
            ->add('confirmer')
            ->add(
                '_action',
                'actions',
                array(
                    'actions' => array(
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
        $queryPhs = $this->modelManager->getEntityManager(Ph::class)->getRepository(Ph::class)->findAllOrderedByName();
        $queryRusticites = $this->modelManager->getEntityManager(Rusticite::class)->getRepository(
            Rusticite::class
        )->findAllOrderedByName();
        $queryPrecipitations = $this->modelManager->getEntityManager(Precipitation::class)->getRepository(
            Precipitation::class
        )->findAllOrderedByName();
        $queryStyles = $this->modelManager->getEntityManager(Style::class)->getRepository(
            Style::class
        )->findAllOrderedByName();
        $queryEnsoleillements = $this->modelManager->getEntityManager(Ensoleillement::class)->getRepository(
            Ensoleillement::class
        )->findAllOrderedByName();

        $formMapper
            ->with('Général')
            ->add('nom')
            ->add('dateCreation', null, array('disabled' => true))
            ->add(
                'client',
                EntityType::class,
                array(
                    'class' => Client::class,
//                'property' => 'nom',
                    'expanded' => false,
                    'multiple' => false,
//                'empty_value' => '-'
                )
            )
            ->add(
                'projetType',
                EntityType::class,
                array(
                    'class' => Creation::class,
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
            )//'empty_value' => '-',
            ->add('budgetMax')
            ->add('surface')
            ->add('arrosageDeuxFoisParSemaine')
            ->end()
            ->with('Coordonnées')
            ->add('adresse')
            ->add('latitude', null, array('required' => false))
            ->add('longitude', null, array('required' => false))
            ->add('altitude')
            ->end()
            ->with('Informations compl.')
            ->add(
                'ph',
                ModelType::class,
                array('multiple' => false, 'expanded' => false, 'required' => false, 'query' => $queryPhs)
            )
            ->add(
                'rusticite',
                ModelType::class,
                array('multiple' => false, 'expanded' => false, 'required' => false, 'query' => $queryRusticites)
            )
            ->add(
                'precipitation',
                ModelType::class,
                array('multiple' => false, 'expanded' => false, 'required' => false, 'query' => $queryPrecipitations)
            )
            ->add(
                'ensoleillement',
                ModelType::class,
                array('multiple' => false, 'expanded' => false, 'required' => false, 'query' => $queryEnsoleillements)
            )
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    protected function configureSideMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        if (!$childAdmin && !in_array($action, array('edit'))) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;

        $id = $admin->getRequest()->get('id');

        $menu->addChild(
            'Projet',
            array('uri' => $admin->generateUrl('edit', array('id' => $id)))
        );

        $menu->addChild(
            'Créations',
            array('uri' => $admin->generateUrl('mjmt_back.admin.creation.list', array('id' => $id)))
        );
    }
}
