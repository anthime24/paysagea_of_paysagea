<?php

namespace App\Front\Form;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class PaymentType extends AbstractType
{
    private $em;
    private $translator;

    public function __construct(EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {
        $this->em = $entityManager;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $optionsMethodePaiement = array();
        $optionsMethodePaiementQuery = $this->em->createQueryBuilder();
        $optionsMethodePaiementQuery->select('t')
            ->from(\App\Core\Entity\EnumerationMethodePaiement::class, 't');

        foreach($optionsMethodePaiementQuery->getQuery()->getResult() as $item) {
            $optionsMethodePaiement[$item->getNom()] = $item->getCle();
        }

        $builder
            ->add(
                'paymentMethod',
                ChoiceType::class,
                array(
                    'label' => 'Mode de paiement :',
                    'choices' => $optionsMethodePaiement,
                    'required' => true,
                    'expanded' => true,
                    'multiple' => false
                )
            );
    }

}
