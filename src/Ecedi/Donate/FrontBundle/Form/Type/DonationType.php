<?php
/**
 * @author Sylvain Gogel <sgogel@ecedi.fr>
 * @copyright 2015 Agence Ecedi
 * @package eDonate
 * @license MIT http://opensource.org/licenses/MIT
 */
namespace Ecedi\Donate\FrontBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Ecedi\Donate\CoreBundle\PaymentMethod\Plugin\PaymentMethodInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class DonationType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    private function buildAmountSelectorSubForm(FormBuilderInterface $form, $tunnel, $options)
    {
        $choices = $options['equivalences'][$tunnel];
        $default = '';

        foreach ($choices as $equivalence) {
            if ($equivalence->isDefault()) {
                $default = $equivalence->getAmount();
            }
        }

        $form->add($tunnel, AmountType::class, [
                'mapped'        => false,
                'label'         => false,
                'required'      => true,
                'choices'       => $this->getEquivalencesOptions($options['equivalences'], $tunnel),
                'min_amount'    => $options['min_amount'],
                'max_amount'    => $options['max_amount'],
                'attr'          => ['class' => 'amount_selector tunnel-'.$tunnel], //used in the JS part
                'default'       => $default,
                'title'         => sprintf('block.amount.%s.title', $tunnel)

            ]);
    }

    /**
     * @since 2.4 flip keys and values and add choices_as_values option
     * @param  FormBuilderInterface $builder [description]
     * @param  array                $options [description]
     * @return [type]               [description]
     */
    private function buildPersonnalDetails(FormBuilderInterface $builder, array $options)
    {
        // Info perso
        $builder->add('civility', ChoiceType::class, [
            'choices'   => array_flip($options['civilities']),
            'required'  => false,
            'label' => 'label.civility',
            'choices_as_values' => true,
        ]);

        $builder->add('company', TextType::class, [
            'required' => FALSE,
            'label' => 'label.company'
        ]);

        $builder->add('firstName', TextType::class, [
            'required' => TRUE,
            'label' => 'label.firstname'
        ]);

        $builder->add('lastName', TextType::class, [
            'required' => TRUE,
            'label' => 'label.lastname'
        ]);

        $builder->add('phone', TextType::class, [
            'required' => FALSE,
            'label' => 'label.phone'
        ]);

        $builder->add('email', RepeatedType::class, [
            'type' => EmailType::class,
            'invalid_message' => 'The email fields must match.',
            'required' => true,
            'first_options'  => array('label' => 'label.email'),
            'second_options' => array('label' => 'label.confirm_email'),
        ]);

        //Address
        $builder->add('addressStreet', TextType::class, [
            'required'  => true,
            'label' => 'label.address'
        ]);

        $builder->add('addressPb', TextType::class, [
            'required'  => false,
            'label' => 'label.postal_box'
        ]);

        $builder->add('addressLiving', TextType::class, [
            'required'  => false,
            'label' => 'label.address_living'
        ]);

        $builder->add('addressExtra', TextType::class, [
            'required'  => false,
            'label' => 'Apartment, floor numbers'
        ]);

        $builder->add('addressZipcode', NumberType::class, [
            'required'  => true,
            'label' => 'label.zipcode'
        ]);

        $builder->add('addressCity', TextType::class, [
            'required'  => true,
            'label' => 'label.city'
        ]);

        $builder->add('addressCountry', CountryType::class, [
            'required'  => true,
            'preferred_choices' => array('FR'),
            'data' => 'FR',
            'label' => 'label.country',
            'choice_translation_domain' => false,
        ]);
    }

    /**
     * @since  2.0.0
     * @since 2.4 flip keys and values and add choices_as_values option
     * @param  Collection $affectations [description]
     * @return array      [description]
     */
    protected function getAffectationChoices(Collection $affectations)
    {
        $choices = [];
        foreach ($affectations as $aff) {
            $choices[$aff->getLabel()] = $aff->getCode();
        }

        return $choices;
    }

    /**
     * Add affectation field according to options
     *
     * @since 2.0.0
     * @since 2.4 flip keys and values and add choices_as_values option
     * @param FormBuilderInterface $builder [description]
     * @param array                $options [description]
     */
    public function buildAffectations(FormBuilderInterface $builder, array $options)
    {
        $affectations = $options['affectations'];

        if ($affectations->count() === 0) {
            $builder->add('affectations', 'hidden', [
                'data' => false,
                'mapped' => false,
            ]);

            return;
        }

        if ($affectations->count() === 1) {
            $builder->add('affectations', 'hidden', [
                'data' => $affectations->first()->getCode(),
                'mapped' => false,
            ]);

            return;
        }

        if ($affectations->count() > 1) {
            $builder->add('affectations', ChoiceType::class, [
                 'choices'   => $this->getAffectationChoices($affectations),
                 'required'  => true,
                 'expanded' => true,
                 'multiple' => false,
                 'label' => 'I want to',
                 'data' => $affectations[0]->getCode(),
                 'mapped' => false,
                 'choices_as_values' => true,
            ]);

            return;
        }
    }

    /**
     * @since 2.4 flip keys and values and add choices_as_values option
     * @param  FormBuilderInterface $builder [description]
     * @param  array                $options [description]
     * @return [type]               [description]
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $tunnels = $this->paymentMethodsToTunnels($options['payment_methods']);

        // equivalences for each tunnels subform
        $tunnelForm = $builder->create('tunnels', FormType::class, array('inherit_data' => true, 'label' => false));

        foreach (array_keys($tunnels) as $key) {
            $this->buildAmountSelectorSubForm($tunnelForm, $key, $options);
        }

        $builder->add($tunnelForm);

        $this->buildAffectations($builder, $options);
        $this->buildPersonnalDetails($builder, $options);

        $builder->add('erf', ChoiceType::class, [
            'choices'   => [
                'by email' => 0,
                'by post' => 1,
            ],
            'required'  => true,
            'expanded' => true,
            'multiple' => false,
            'label' => 'I prefer to receive my tax receipt',
            'data' => 0,
            'mapped' => false,
            'choices_as_values' => true,
            ]
        );

        $builder->add('optin', CheckboxType::class, [
            'required' => false,
            'label' => 'I agree to receive informations from Association XY',
        ]);

        // payment methods for each tunnels subform
        $pmForm = $builder->create('payment_method', FormType::class, [
            'inherit_data' => true,
            'label' => false,
        ]);

        foreach ($options['payment_methods'] as $pm) {
            $pmForm->add($pm->getId(), SubmitType::class, [
                    'label'         => $pm->getName(),
                    'attr'          => ['class' => 'btn btn-primary tunnel-'.$pm->getTunnel()], //used in the JS part
                ]);
        }

        $builder->add($pmForm);
    }

    /**
     * @since 2.4 flip keys and values and add choices_as_values option
     *
     * @param  [type] $equivalences [description]
     * @param  [type] $tunnel       [description]
     * @return [type] [description]
     */
    protected function getEquivalencesOptions($equivalences, $tunnel = PaymentMethodInterface::TUNNEL_SPOT)
    {
        $options = [];

        foreach ($equivalences[$tunnel] as $equivalence) {
            $options[$equivalence->getAmount()] = $equivalence->getLabel();
        }
        $options['manual'] = 'Other amount';

        return array_flip($options);
    }

    /**
     * transform a list of payment methods to an array with key is tunnel name
     * @param  array  $paymentMethods a list of PaymentMethodInterface
     * @return [type] [description]
     */
    protected function paymentMethodsToTunnels($paymentMethods)
    {
        $tunnels = [];
        foreach ($paymentMethods as $pm) {
            $tunnels[$pm->getTunnel()][] = $pm;
        }

        return $tunnels;
    }

    /**
     * {@inheritdoc}
     * @since 2.4 use new method signatire since sf 2.7
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection'   => false,
            'civilities' => [],
            'equivalences' => [],
            'payment_methods' => [],
            'min_amount' => 5,
            'max_amount' => 4000,
            'affectations' => [],
        ]);
    }
}
