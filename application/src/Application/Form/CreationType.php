<?php

namespace App\Application\Form;

use App\Core\Entity\Creation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class CreationType extends AbstractType
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
            ->add('nom', TextType::class, array('label' => $this->translator->trans('Nom de la création'), 'required' => true))
            ->add('creationType', null, array('label' => $this->translator->trans('Type de la création'), 'required' => true))
            ->add('ensoleillement', null, array('label' => $this->translator->trans('Ensoleillement pendant l\'été')))
            ->add('style', null, array('label' => $this->translator->trans('Style souhaité')))
            ->add('arrosageDeuxFoisSemaine', CheckboxType::class, array(
                'mapped' => false,
                'label' => $this->translator->trans("Arrosage des plantes manuel ou automatique aux moins 2 fois par semaine en période sèche"),
                'data' => isset($options['data']) ? $options['data']->getProjet()->getArrosageDeuxFoisParSemaine() : null
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => Creation::class,
            )
        );
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'marque_blanche_appbundle_creation';
    }

}
