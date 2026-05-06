<?php

namespace App\Back\Admin;

use App\Core\Entity\Client;
use App\Core\Entity\CodePromo;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ClientCodePromoAdmin extends AbstractAdmin
{
    /**
     * {@inheritdoc}
     */
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'dateUtilisation'
    );

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add(
                'dateUtilisation',
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
                'codePromo',
                'sonata_type_model',
                array(
                    'class' => CodePromo::class,
                    'property' => ''
                )
            )
            ->add('dateUtilisation')
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
            ->add('dateUtilisation');
    }
}
