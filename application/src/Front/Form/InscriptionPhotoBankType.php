<?php

namespace App\Front\Form;

use App\Core\Entity\BanquePhoto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Contracts\Translation\TranslatorInterface;

class InscriptionPhotoBankType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', FileType::class, array('label' => false, 'required' => false, 'constraints' => [new File([
                "maxSize" => "10M",
                "mimeTypes" => [
                    "image/png",
                    "image/jpg",
                    "image/jpeg",
                    "image/gif",
                    "image/heic",
                    "image/heif",
                    "image/x-heic"
                ],
                "mimeTypesMessage" => "Veuillez envoyer une image au format png, jpg, jpeg, gif ou heic (iPhone), de 10 mégas octets maximum"
            ])]))
            ->add(
                'nom',
                TextType::class,
                array('label' => false, 'attr' => array('placeholder' => $this->translator->trans('Nom de la photo')), 'required' => false)
            )
//            ->add(
//                'email',
//                TextType::class,
//                array('label' => false, 'attr' => array('placeholder' => $this->translator->trans('Adresse e-mail')), 'required' => false)
//            )
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => BanquePhoto::class
            )
        );
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'mjmt_front_inscription_photo_bank';
    }

}
