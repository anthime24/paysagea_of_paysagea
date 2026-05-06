<?php

namespace App\Back\Admin;

use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ClientAdmin extends AbstractAdmin
{
    /**
     * {@inheritdoc}
     */
    protected $datagridValues = array(
        'confirmer' => array('value' => true),
        '_sort_order' => 'DESC',
        '_sort_by' => 'dateInscription'
    );

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('email')
            ->add('nom')
            ->add('prenom')
            ->add('confirmer');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('email')
            ->add('nom')
            ->add('prenom')
            ->add('creditPhotos')
            ->add('typePersonne')
            ->add('dateInscription')
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
        $formMapper
            ->with('Client')
            ->add('email')
            ->add('nom')
            ->add('prenom')
            ->add('telephone')
            ->add('dateDerniereConnexion', null, array('disabled' => true))
            ->add('dateInscription', null, array('disabled' => true))
            ->add('confirmer')
            ->end()
            ->with('Etat')
            ->add('creditPhotos')
            ->add('creditConseilsProfessionnel')
            ->add('creditAidePaysagiste')
            ->add('accesCompletPlantesObjets')
            ->add('creationNonPublique')
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
            'Client',
            array('uri' => $admin->generateUrl('edit', array('id' => $id)))
        );

        $menu->addChild(
            'Projets',
            array('uri' => $admin->generateUrl('mjmt_back.admin.projet.list', array('id' => $id)))
        );

        $menu->addChild(
            'Paiements',
            array('uri' => $admin->generateUrl('mjmt_back.admin.client_paiement.list', array('id' => $id)))
        );

        $menu->addChild(
            'Offres',
            array('uri' => $admin->generateUrl('mjmt_back.admin.client_offre.list', array('id' => $id)))
        );

        $menu->addChild(
            'Codes promo',
            array('uri' => $admin->generateUrl('mjmt_back.admin.client_code_promo.list', array('id' => $id)))
        );
    }
}
