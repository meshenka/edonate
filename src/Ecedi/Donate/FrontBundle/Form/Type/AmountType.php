<?php
/**
 * @author Sylvain Gogel <sgogel@ecedi.fr>
 * @copyright Agence Ecedi (c) 2016
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ecedi\Donate\FrontBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Ecedi\Donate\FrontBundle\Form\DataTransformer\AmountChoiceToIntentAmountTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

class AmountType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    //TODO find a way to remove @translator
    /**
     * @since 2.4 flip keys and values and add choices_as_values option
     *
     * @param FormBuilderInterface $builder [description]
     * @param array                $options [description]
     *
     * @return [type] [description]
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Ajout d'un champ de saisi manuel si voulu
        $options['choices']['Other amount'] = 'manual';

        $builder
            ->addViewTransformer(new AmountChoiceToIntentAmountTransformer([
                'manual',
                'preselected',
            ]))
            ->add('preselected', ChoiceType::class, [
                'choices' => $options['choices'],
                'required' => false,
                'expanded' => true,
                'multiple' => false,
                'label' => false,
                'data' => $options['default'],
                //'placeholder' => false,
                'choices_as_values' => true,
                'choice_value' => function ($choice) {
                    return $choice;
                },
                'choice_translation_domain' => false,
            ])
            ->add('manual', MoneyType::class, [
                'currency' => 'EUR',
                'required' => false,
                'label' => 'amount.other',
                'scale' => 0,
                'constraints' => [
                    new Assert\Range(
                        [
                          'min' => $options['min_amount'],
                          'max' => $options['max_amount'],
                          'minMessage' => $this->translator->trans('amount.min', ['%amount%' => $options['min_amount']], 'validators'),
                          'maxMessage' => $this->translator->trans('amount.max', ['%amount%' => $options['max_amount']], 'validators'),
                        ]
                    ),
                ],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['title'] = $options['title'];
    }
    /**
     * {@inheritdoc}
     *
     * @since 2.4 use new method signature since sf 2.7
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => [],
            'min_amount' => 5,
            'max_amount' => 4000,
            'default' => '',
            'title' => '',
        ]);
    }
}
