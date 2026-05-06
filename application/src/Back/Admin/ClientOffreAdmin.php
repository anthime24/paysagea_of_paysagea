<?php

namespace App\Back\Admin;

use App\Core\Entity\Client;
use App\Core\Entity\Offre;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ClientOffreAdmin extends AbstractAdmin
{
    /**
     * {@inheritdoc}
     */
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'dateAjout'
    );

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('offre')
            ->add(
                'dateAjout',
                'doctrine_orm_date_range',
                array(),
                null,
                array('required' => false)
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add(
                'client',
                'sonata_type_model',
                array(
                    'class' => Client::class,
                    'property' => ''
                )
            )
            ->add(
                'offre',
                'sonata_type_model',
                array(
                    'class' => Offre::class,
                    'property' => ''
                )
            )
            ->add('dateAjout')
            ->add(
                '_action',
                'actions',
                array(
                    'actions' => array(
                        'delete' => array()
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
            ->add('dateAjout');
    }
}
