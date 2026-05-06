<?php

namespace App\Back\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomFileUploadType extends AbstractType
{
    public function getParent()
    {
        return FileType::class;
    }

    public function getBlockPrefix()
    {
        return 'mjmt_custom_file_upload_type';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('filePropertyGetterName');
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $view->vars = array_merge(
            $view->vars,
            array(
                'filePropertyGetterName' => $options['filePropertyGetterName']
            )
        );
    }
}
