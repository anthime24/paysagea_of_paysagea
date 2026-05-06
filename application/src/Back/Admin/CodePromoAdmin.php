<?php

namespace App\Back\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class CodePromoAdmin extends AbstractAdmin
{
    /**
     * {@inheritdoc}
     */
    protected $datagridValues = array(
        '_sort_order' => 'ASC',
        '_sort_by' => 'code'
    );

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('code')
            ->add('actif');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('nom')
            ->add('code')
            ->add('dateDebut')
            ->add('dateFin')
            ->add('valeur')
            ->add('pourcentage')
            ->add('actif')
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
            ->add('nom')
            ->add('code')
            ->add('dateDebut')
            ->add('dateFin')
            ->add('nbUtilisations')
            ->add('nbUtilisationsCompteur', null, array('disabled' => true))
            ->add('nbUtilisationsParClient')
            ->add('valeur')
            ->add('pourcentage')
            ->add('offres')
            ->add('actif');
    }
}
