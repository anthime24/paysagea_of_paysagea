<?php

namespace App\Back\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\File;

class BanquePhotoAdmin extends AbstractAdmin
{

    /**
     * {@inheritdoc}
     */
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'dateCreation',
        'deleted' => array('value' => 0)
    );

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);

        $collection->add(
            'proportions',
            'proportions',
            array('_controller' => 'App\Back\Controller\BanquePhotoAdminController::proportions')
        );
    }

    public function getFilterParameters()
    {
        $parameters = parent::getFilterParameters();
        if(!isset($parameters['deleted'])) {
            $parameters['deleted'] = array(
                'type' => null,
                'value' => 0
            );
        }

        return $parameters;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('client')
            ->add('nom')
            ->add('email')
            ->add('banquePhotoType')
            ->add('public')
            ->add('confirmer')
            ->add('deleted', \Sonata\DoctrineORMAdminBundle\Filter\CallbackFilter::class, array(
                'show_filter' => true,
                'callback' => function($query, $alias, $field, $value) {
                    if($value['value'] == 1) {
                        $query->andWhere($alias.'.deleted = 1');
                    } else {
                        $query->andWhere($query->expr()->orX(
                            $query->expr()->eq($alias.'.deleted', 0),
                            $query->expr()->isNull($alias.'.deleted')
                        ));
                    }
                },
                'field_type' => ChoiceType::class,
                'field_options' => array(
                    'choices' => array(
                        'Oui' => '1',
                        'Non' => '0'
                    )
                )
            ));
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('custom', 'string', array('template' => 'back/entite_photo/list_custom.html.twig'))
            ->add('nom')
            ->add('client', null, array('sortable' => true))
            ->add('banquePhotoType.nom', null, array('label' => 'Type', 'sortable' => true))
            ->add('email', null, array('label' => 'Email', 'sortable' => true))
            ->add('public')
            ->add('confirmer')
            ->add(
                'recevoirInfosPartenaires',
                'string',
                array('template' => 'back/entite_photo/list_custom_recevoir_infos_partenaires.html.twig')
            )
            ->add('deleted', null, array('label' => 'Supprimé'))
            ->add(
                '_action',
                'actions',
                array(
                    'actions' => array(
                        'proportions' => array('template' => 'back/banque_photo/list__action_proportions.html.twig'),
                        'edit' => array(),
                        'delete' => array(),
                        'view_picture' => array('template' => 'back/banque_photo/list_action_view_picture.html.twig'),
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
            ->with('Photo')
            ->add('nom')
            ->add('dateCreation', null, array('disabled' => true))
            ->add('client', null, array('required' => false, 'empty_data' => '-'))
            ->add('banquePhotoType')
            ->add('file', FileType::class, array(
                'required' => false
            ))
            ->add('public')
            ->add('confirmer')
            ->end()
            ->with('Informations')
            ->add('poids', null, array('disabled' => true, 'help' => 'octets'))
            ->add('largeur', null, array('disabled' => true, 'help' => 'pixels'))
            ->add('hauteur', null, array('disabled' => true, 'help' => 'pixels'))
            ->add('type', null, array('disabled' => true))
            ->end()
            ->with('Email')
            ->add('email', null, array('disabled' => true))
            ->end();
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

    public function getExportFields()
    {
        set_time_limit(120);
        return array(
            'date_creation' => 'dateCreation',
            'email' => 'email',
            'recevoir_infos_partenaire' => 'hasClientProjetRecevoirInfosPartenaire',
            'adresse' => 'allAdressesClient'
        );
    }

}
