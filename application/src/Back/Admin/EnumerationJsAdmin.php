<?php

namespace App\Back\Admin;

use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

use App\Back\Form\Type\TranslatedType;
use Sonata\AdminBundle\Route\RouteCollection;

class EnumerationJsAdmin extends AbstractAdmin
{
    /**
     * {@inheritdoc}
     */
    protected $datagridValues = array(
        '_sort_order' => 'ASC',
        '_sort_by' => 'min'
    );


    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create');
        $collection->remove('delete');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('cle')
            ->add('nom');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('cle')
            ->add('nom')
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
            ->add('nom', TranslatedType::class, array(
                'propertyName' => 'nom'
            ));
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
