<?php

namespace App\Back\Admin;

use App\Core\Entity\Client;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ClientPaiementAdmin extends AbstractAdmin
{

    /**
     * {@inheritdoc}
     */
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'datePaiement'
    );

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('datePaiement', 'doctrine_orm_date_range', array(), null, array('required' => false))
            ->add('valide');
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
            ->add('datePaiement')
            ->add('montantPaiement')
            ->add('numTransaction')
            ->add('client.email')
            ->add('client.typePersonne')
            ->add('valide')
            ->add(
                '_action',
                'actions',
                array(
                    'actions' => array(
                        'edit' => array()
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
            ->add('datePaiement')
            ->add('montantPaiement')
            ->add('numTransaction')
            ->add('reponseCode')
            ->add('reponseCodeTexte')
            ->add('reference')
            ->add('valide');
    }
}
