<?php

namespace App\Back\Admin;

use App\Back\Form\Type\CustomFileUploadType;
use App\Back\Form\Type\PictogrammeType;
use App\Back\Form\Type\TranslatedType;
use App\Core\Entity\Categorie;
use App\Core\Entity\Couleur;
use App\Core\Entity\Entite;
use App\Core\Entity\Mois;
use App\Core\Entity\Rusticite;
use App\Core\Entity\Style;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Count;

class EntiteAdmin extends AbstractAdmin
{
    /**
     * {@inheritdoc}
     */
    protected $datagridValues = array(
        '_sort_order' => 'ASC',
        '_sort_by' => 'type'
    );

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);

        $collection->add(
            'duplicate',
            'duplicate',
            array('_controller' => 'App\Back\Controller\EntiteAdminController::duplicate')
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('entiteType')
            ->add('id', null, array('show_filter' => true))
            ->add('nom', null, array('show_filter' => true))
            ->add('acronyme')
            ->add('nomVernaculaire', null, array('show_filter' => true))
            ->add('actif', null, array('show_filter' => true));
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('entiteType.nom', null, array('label' => 'Entite Type', 'sortable' => true))
            ->add('nom')
            ->add('acronyme')
            ->add('nomVernaculaire')
            ->add('actif')
            ->add(
                '_action',
                'actions',
                array(
                    'actions' => array(
                        'duplicate' => array('template' => 'back/entite/list__action_duplicate.html.twig'),
                        'edit' => array(),
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
        $queryCategories = $this->modelManager->getEntityManager(Categorie::class)->getRepository(
            Categorie::class
        )->findAllOrderedByName();
        $queryStyles = $this->modelManager->getEntityManager(Style::class)->getRepository(
            Style::class
        )->findAllOrderedByName();
        $queryCouleurs = $this->modelManager->getEntityManager(Couleur::class)->getRepository(
            Couleur::class
        )->findAllOrderedByName();
        $queryRusticites = $this->modelManager->getEntityManager(Rusticite::class)->getRepository(
            Rusticite::class
        )->findAllOrderedByName();

        $this->setTemplate('edit', 'back/entite/edit.html.twig');

        $formMapper
            ->with('Général')
            ->add('acronyme', TranslatedType::class, array(
                'propertyName' => 'acronyme'
            ))
            ->add('nom', TranslatedType::class, array(
                'propertyName' => 'nom'
            ))
            ->add('nomVernaculaire', TranslatedType::class, array(
                'propertyName' => 'nomVernaculaire',
                'propertyOptions' => array(
                    'attr' => array('class' => 'entite-plante span5'),
                    'required' => false
                )
            ))
            ->add('entiteType', null, array('label' => 'Type'))
            ->add('entiteSousTypeMultiples', null, array('multiple' => true, 'label' => 'Sous-type'))
            ->add('hauteurPot', TextType::class, array('label' => 'Hauteur pot (cm)'))
            ->add('diametrePot', TextType::class, array('label' => 'Diamètre pot (cm)'))
            ->add('hauteurFinale', TextType::class, array('label' => 'Hauteur finale (cm)'))
            ->add('diametreFinal', TextType::class, array('label' => 'Diamètre final (cm)'))
            ->add('prixMini', TextType::class, array('label' => 'Prix mini. (€)'))
            ->add('gratuit', null, array('required' => false))
            ->end()
            ->with('Lasso')
            ->add('lasso', null, array('required' => false))
            ->add(
                'fileLasso',
                CustomFileUploadType::class,
                array(
                    'required' => false,
                    'label' => false,
                    'filePropertyGetterName' => 'getLassoPhoto'
                )
            )
            ->end()
            ->with('Pictogramme', ['class' => 'col-md-12 inline-form'])
            ->add(
                'pictogrammeForm',
                PictogrammeType::class,
                array(
                    'data_class' => Entite::class,
                    'label' => false
                )
            )
            ->end()
            ->with('Infos compl.')
            ->add('entretien', null, array())
            ->add('annuelle')
            ->add(
                'toxiqueAlimentaire',
                ChoiceType::class,
                array(
                    'label' => 'Toxique/Alimentaire',
                    'required' => false,
                    'choices' => array('' => '-', 'toxique' => 'toxique', 'alimentaire' => 'alimentaire'),
                    'attr' => array('class' => 'entite-plante span5')
                )
            )
            ->add(
                'caducPersistant',
                ChoiceType::class,
                array(
                    'label' => 'Caduc/Persistant',
                    'required' => false,
                    'choices' => array('' => '-', 'caduc' => 'caduc', 'persistant' => 'persistant'),
                    'attr' => array('class' => 'entite-plante span5')
                )
            )
            ->add('typeSols', ModelType::class, array('multiple' => true, 'expanded' => true, 'required' => false))
            ->add(
                'ensoleillements',
                ModelType::class,
                array('multiple' => true, 'expanded' => true, 'required' => false)
            )
            ->add('besoinEauMultiples', null, array('label' => 'Besoin Eau', 'multiple' => true))
            ->add('rusticiteValeur')
            ->add(
                'rusticiteMultiples',
                ModelType::class,
                array(
                    'label' => 'Rusticite',
                    'multiple' => true,
                    'query' => $queryRusticites,
                    'constraints' => array(
                        new Count(
                            array(
                                'min' => 0,
                                'minMessage' => 'Vous devez définir au moins une valeur pour la rusticité'
                            )
                        )
                    )
                )
            )
            ->end()
            ->with('Conseils')
            ->add('conseilAutomne', TranslatedType::class, array(
                'propertyName' => 'conseilAutomne',
                'propertyOptions' => array(
                    'attr' => array('class' => 'entite-plante span5'),
                    'required' => false
                )
            ))
            ->add('conseilHiver', TranslatedType::class, array(
                'propertyName' => 'conseilHiver',
                'propertyOptions' => array(
                    'attr' => array('class' => 'entite-plante span5'),
                    'required' => false
                )
            ))
            ->add('conseilPrintemps', TranslatedType::class, array(
                'propertyName' => 'conseilPrintemps',
                'propertyOptions' => array(
                    'attr' => array('class' => 'entite-plante span5'),
                    'required' => false
                )
            ))
            ->add('conseilEte', TranslatedType::class, array(
                'propertyName' => 'conseilEte',
                'propertyOptions' => array(
                    'attr' => array('class' => 'entite-plante span5'),
                    'required' => false
                )
            ))
            ->add('divers', TranslatedType::class, array(
                'propertyName' => 'divers',
                'propertyOptions' => array(
                    'required' => false
                )
            ))
            ->end()
            ->with('Couleurs')
            ->add(
                'couleurs',
                ModelType::class,
                array('multiple' => true, 'expanded' => true, 'required' => false, 'query' => $queryCouleurs)
            )
            ->end()
            ->with('Couleurs Fleurs')
            ->add(
                'couleurFleurs',
                ModelType::class,
                array('multiple' => true, 'expanded' => true, 'required' => false, 'query' => $queryCouleurs)
            )
            ->end()
            ->with('Categories')
            ->add(
                'categories',
                ModelType::class,
                array('multiple' => true, 'expanded' => true, 'required' => false, 'query' => $queryCategories)
            )
            ->end()
            ->with('Styles')
            ->add(
                'styles',
                ModelType::class,
                array('multiple' => true, 'expanded' => true, 'required' => false, 'query' => $queryStyles)
            )
            ->end()
            ->with('Mois floraison')
            ->add(
                'mois',
                EntityType::class,
                array(
                    'class' => Mois::class,
                    'attr' => array('class' => 'entite-plante unstyled span5'),
//                'property' => 'nom',
                    'multiple' => true,
                    'expanded' => true,
                    'label' => 'Mois',
                    'required' => false
                )
            )
            ->end();
    }

    public function getFormTheme()
    {
        return array_merge(
            parent::getFormTheme(),
            array(
                'back/form/custom_file_upload.html.twig',
                'back/form/pictogramme.html.twig',
                'back/form/translatedField.html.twig'
            )
        );
    }


    /**
     * {@inheritdoc}
     */
    protected function configureSideMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        if (!$childAdmin && !in_array($action, array('edit'))) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;
        $id = $admin->getRequest()->get('id');

        $menu->addChild(
            'Edition',
            array('uri' => $admin->generateUrl('edit', array('id' => $id)))
        );


        $menu->addChild(
            'Photos',
            array('uri' => $admin->generateUrl('mjmt_back.admin.entite_photo.list', array('id' => $id)))
        );


        $menu->addChild(
            'Créations',
            array('uri' => $admin->generateUrl('mjmt_back.admin.creation_entite.list', array('id' => $id)))
        );
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
