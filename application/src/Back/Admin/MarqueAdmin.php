<?php

namespace App\Back\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class MarqueAdmin extends AbstractAdmin
{
    /**
     * {@inheritdoc}
     */
    protected $datagridValues = array(
        '_sort_order' => 'ASC',
        '_sort_by' => 'nom'
    );

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('nom')
            ->add('entiteTypes');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('custom', 'string', array('template' => 'back/marque/list_custom.html.twig'))
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
            ->add('file', FileType::class, array('required' => false))
            ->add('entiteTypes');
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist($object)
    {
        $object->preUpload();
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($object)
    {
        $object->preUpload();
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
