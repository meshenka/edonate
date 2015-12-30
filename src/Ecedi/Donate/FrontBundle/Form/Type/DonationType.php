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
        $builder->add('civility', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
            'choices'   => array_flip($options['civilities']),
            'required'  => false,
            'label' => 'Civility',
            'choices_as_values' => true,
        ]);

        $builder->add('company', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'required' => FALSE,
            'label' => 'Company'
        ]);

        $builder->add('firstName', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'required' => TRUE,
            'label' => 'First name'
        ]);

        $builder->add('lastName', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'required' => TRUE,
            'label' => 'Last name'
        ]);

        $builder->add('phone', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'required' => FALSE,
            'label' => 'Phone'
        ]);

        $builder->add('email', 'Symfony\Component\Form\Extension\Core\Type\RepeatedType', [
            'type' => 'Symfony\Component\Form\Extension\Core\Type\EmailType',
            'invalid_message' => 'The email fields must match.',
            'required' => true,
            'first_options'  => array('label' => 'Email'),
            'second_options' => array('label' => 'Repeat Email'),
        ]);

        //Address
        $builder->add('addressStreet', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'required'  => true,
            'label' => 'Address'
        ]);

        $builder->add('addressPb', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'required'  => false,
            'label' => 'Locality, post box'
        ]);

        $builder->add('addressLiving', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'required'  => false,
            'label' => 'Living with'
        ]);

        $builder->add('addressExtra', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'required'  => false,
            'label' => 'Apartment, floor numbers'
        ]);

        $builder->add('addressZipcode', 'Symfony\Component\Form\Extension\Core\Type\NumberType', [
            'required'  => true,
            'label' => 'Zipcode'
        ]);

        $builder->add('addressCity', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
            'required'  => true,
            'label' => 'City'
        ]);

        $builder->add('addressCountry', 'Symfony\Component\Form\Extension\Core\Type\CountryType', [
            'required'  => true,
            'preferred_choices' => array('FR'),
            'data' => 'FR',
            'label' => 'Country'
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
            $builder->add('affectations', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
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
        $tunnelForm = $builder->create('tunnels', 'Symfony\Component\Form\Extension\Core\Type\FormType', array('inherit_data' => true, 'label' => false));

        foreach (array_keys($tunnels) as $key) {
            $this->buildAmountSelectorSubForm($tunnelForm, $key, $options);
        }

        $builder->add($tunnelForm);

        $this->buildAffectations($builder, $options);
        $this->buildPersonnalDetails($builder, $options);

        $builder->add('erf', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
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

        $builder->add('optin', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', [
            'required' => false,
            'label' => 'I agree to receive informations from Association XY',
        ]);

        // payment methods for each tunnels subform
        $pmForm = $builder->create('payment_method', 'Symfony\Component\Form\Extension\Core\Type\FormType', ['inherit_data' => true, 'label' => false]);

        foreach ($options['payment_methods'] as $pm) {
            $pmForm->add($pm->getId(), 'Symfony\Component\Form\Extension\Core\Type\SubmitType', [
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
