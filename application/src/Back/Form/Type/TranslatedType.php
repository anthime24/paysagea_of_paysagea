<?php

namespace App\Back\Form\Type;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Core\Utility\Utility;

class TranslatedType extends AbstractType
{
    private $em;
    private $request;

    public function __construct(EntityManagerInterface $em, RequestStack $rs)
    {
        $this->em = $em;
        $this->request = $rs->getCurrentRequest();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $propertyName = $options['propertyName'];
        $propertyType = $options['propertyType'];
        $propertyOptions = $options['propertyOptions'];
        $propertyOptions['label'] = false;

        $builder->add($propertyName, $propertyType, $propertyOptions);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('propertyName');
        $resolver->setDefaults(
            [
                'propertyType' => null,
                'propertyOptions' => array(),
                'inherit_data' => true,
            ]
        );
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $selectedLanguage = $this->request->get('tl', null);
        $translatedObjectOriginalValue = null;
        if (isset($view->vars['data']) && $view->vars['data'] !== null) {
            try {
                $parentType = get_class($view->vars['data']);
                $query = $this->em->createQueryBuilder();
                $query->select('t')
                      ->from($parentType, 't')
                      ->where('t.id = :id')
                      ->setParameter(':id', $view->vars['data']->getId());

                $query->getQuery()->setHint(\Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, 'fr');
                $res = $query->getQuery()->getArrayResult();

                if(count($res) > 0) {
                    $translatedObjectOriginalValue = $res[0];
                }
            } catch (\Exception $ex) {}
        }

        $propertyToAdd = array(
            'translatedType' => true
        );

        if($selectedLanguage != null && $selectedLanguage != 'fr' && $translatedObjectOriginalValue !== null && isset($translatedObjectOriginalValue[$options['propertyName']])) {
            $translatedPropertyOriginalValue = $translatedObjectOriginalValue[$options['propertyName']];
            $propertyToAdd['translatedPropertyOriginalValue'] = Utility::excerpt($translatedObjectOriginalValue[$options['propertyName']], 500);
        }

        $view->vars = array_merge(
            $view->vars,
            $propertyToAdd
        );
    }

    public function getBlockPrefix()
    {
        return 'mjmt_translated_type';
    }
}
