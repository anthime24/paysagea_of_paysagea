<?php

namespace App\Back\Admin;

use FOS\UserBundle\Event\FormEvent;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

use App\Back\Form\Type\TranslatedType;
use Symfony\Component\Form\FormEvents;
use Cocur\Slugify\Slugify;

class CmsAdmin extends AbstractAdmin
{
    /**
     * {@inheritdoc}
     */
    protected $datagridValues = array(
        '_sort_order' => 'ASC',
        '_sort_by' => 'ordre'
    );

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('titre')
            ->add('slug');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('titre')
            ->add('slug')
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
        $requiredFields = array('url', 'titre', 'texte');
        $formMapper
            ->add('url', TranslatedType::class, array(
                'propertyName' => 'url',
                'propertyOptions' => array(
                    'required' => true
                )
            ))
            ->add('titre', TranslatedType::class, array(
                'propertyName' => 'titre'
            ))
            ->add('texte', TranslatedType::class, array(
                'propertyName' => 'texte',
                'propertyType' => TextareaType::class,
                'propertyOptions' => array(
                    'attr' => array('class' => 'tinymce', 'data-theme' => 'advanced', 'style' => 'margin-top: 20px;')
                )
            ))
            ->add('metaTitle',TranslatedType::class, array(
                'propertyName' => 'metaTitle',
                'propertyOptions' => array(
                    'required' => false
                )
            ))
            ->add('metaDescription',TranslatedType::class, array(
                'propertyName' => 'metaDescription',
                'propertyOptions' => array(
                    'required' => false
                )
            ))
            ->add('ordre')
            ->add(
                'slug',
                null,
                array(
                    'label' => 'slug',
                    'help' => '&#8593; (nom de la page au format url. Exemple : "a-propos" pour "A propos")',
                    'attr' => array('readonly' => true)
                )
            )
        ;

        $formMapper->getFormBuilder()->addEventListener(FormEvents::PRE_SET_DATA, function(\Symfony\Component\Form\Event\PreSetDataEvent $event) use ($requiredFields) {
            $request = $this->getRequest();
            $entity = $event->getData();
            $form = $event->getForm();

            if($entity !== null && $entity->getSlug() == 'accueil') {
                //on désactive l'url
                $fieldInfo = $form->get('url')->getConfig()->getOptions();
                unset($fieldInfo['propertyOptions']);
                $fieldInfo['attr'] = array('readonly' => true);
                $form->add('url', TranslatedType::class, $fieldInfo);
            } else if($entity !== null && $entity->getAdministrable() !== true) {
                //on désactive le titre
                $fieldInfo = $form->get('titre')->getConfig()->getOptions();
                $fieldInfo['attr'] = array('readonly' => true);
                $form->add('titre', TranslatedType::class, $fieldInfo);

                //on désactive le texte
                $fieldInfo = $form->get('texte')->getConfig()->getOptions();
                $fieldInfo['propertyOptions']['attr'] = array('readonly' => true);
                $form->add('texte',TranslatedType::class, $fieldInfo);
            }

            //désactivation du caractère obligatoire pour les champs traduits quand différent de la langue de base
            if($form !== null && $request->query->has('tl') && $request->query->get('tl') != 'fr') {
                foreach($requiredFields as $requiredFieldName) {
                    $fieldType = $form->get($requiredFieldName)->getConfig()->getType()->getInnerType();
                    $fieldType = get_class($fieldType);
                    $fieldInfo = $form->get($requiredFieldName)->getConfig()->getOptions();

                    $propertyOptions = isset($fieldInfo['propertyOptions']) ? $fieldInfo['propertyOptions'] : array();
                    $propertyOptions['required'] = false;
                    $fieldInfo['propertyOptions'] = $propertyOptions;

                    $form->add($requiredFieldName, $fieldType, $fieldInfo);
                }
            }
        });

        $formMapper->getFormBuilder()->addEventListener(FormEvents::SUBMIT, function(\Symfony\Component\Form\Event\SubmitEvent $event) {
            $entity = $event->getData();
            $form = $event->getForm();

            if($entity !== null && $entity->getUrl() !== null && trim($entity->getUrl()) != "") {
                $slugify = new Slugify();

                $url = $slugify->slugify($entity->getUrl());
                $entity->setUrl($url);
            }
        });
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
