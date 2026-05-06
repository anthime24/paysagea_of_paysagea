<?php

namespace App\Back\Admin;

use App\Back\Form\Type\TranslatedType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class StyleAdmin extends AbstractAdmin
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
            ->add('nom');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('custom', 'string', array('template' => 'back/style/list_custom.html.twig', 'label' => 'Aperçu'))
            ->add('nom')
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
            ->add('file', FileType::class, array('label' => 'Image', 'required' => false));
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
