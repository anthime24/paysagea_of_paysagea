<?php

namespace App\Front\Form;

use App\Core\Entity\Offre;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class ClientOfferType extends AbstractType
{
    /**
     * @var Security
     */
    private $security;
    private $translator;

    public function __construct(Security $security, TranslatorInterface $translator)
    {
        $this->security = $security;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->security->getUser();

        $builder
            ->add(
                'offre',
                EntityType::class,
                array(
                    'class' => Offre::class,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('o')
                            ->where('o.prix > 0')
                            ->orderBy('o.prix', 'ASC');
                    },
                    'label' => false,
                    'choice_label' => function ($choice, $key, $value) {
                        return $this->translator->trans('Choisir');
                    },
                    'expanded' => true,
                    'multiple' => false,
                    'required' => true,
                    'constraints' => new NotBlank()
                )
            );

        if (!$user || !$user->getCreationNonPublique()) {
            $builder->add(
                'creationNonPublique',
                CheckboxType::class,
                array(
                    'required' => false,
                    'label' =>  $this->translator->trans('Je souhaite que mes créations ne soient pas visibles par les autres utilisateurs (option à 1€).')
                )
            );
        }
    }

}
