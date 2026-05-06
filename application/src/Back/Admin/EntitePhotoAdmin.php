<?php

namespace App\Back\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class EntitePhotoAdmin extends AbstractAdmin
{

    /**
     * {@inheritdoc}
     */
    protected $parentAssociationMapping = 'entite';

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('nom')
            ->add('annee')
            ->add('entitePhotoEtat');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('custom', 'string', array('template' => 'back/entite_photo/list_custom.html.twig'))
            ->add('principale')
            ->add('annee')
            ->add('entitePhotoEtat')
            ->add('hauteurEntite')
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
            ->add('description')
            ->add('principale')
            ->add('annee', null, array('required' => true))
            ->add('entitePhotoEtat', null, array('label' => 'Etat', 'required' => true))
            ->add('hauteurEntite', null, array('label' => 'Hauteur'))
            ->add('file', FileType::class, array('required' => false));
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
