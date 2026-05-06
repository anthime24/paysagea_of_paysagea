<?php

namespace App\Back\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

use App\Back\Form\Type\TranslatedType;

class EnsoleillementAdmin extends AbstractAdmin
{
    /**
     * {@inheritdoc}
     */
    protected $datagridValues = array(
        '_sort_order' => 'ASC',
        '_sort_by' => 'min'
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
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('nom')
            ->add('min')
            ->add('max')
            ->add('unite')
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
            ->add('nom', TranslatedType::class, array(
                'propertyName' => 'nom'
            ))
            ->add('min')
            ->add('max')
            ->add('unite');
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
