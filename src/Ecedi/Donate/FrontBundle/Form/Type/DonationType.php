<?php
namespace Ecedi\Donate\FrontBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Ecedi\Donate\CoreBundle\PaymentMethod\Plugin\PaymentMethodInterface;
use Symfony\Component\Translation\TranslatorInterface;

class DonationType extends AbstractType
{

    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;

    }

    private function buildAmountSelectorSubForm(FormBuilderInterface $form, $tunnel, $options) {        
        $form->add($tunnel, 'amount_selector', [
                'mapped'        => false,
                'label'         => false,
                'required'      => true,
                'choices'       => $this->getEquivalencesOptions($options['equivalences'], $tunnel),
                'min_amount'    => $options['min_amount'],
                'max_amount'    => $options['max_amount'],
                'attr'          => ['class'=>'amount_selector tunnel-' . $tunnel], //used in the JS part                
            ]);        
    }

    private function buildPersonnalDetails(FormBuilderInterface $builder, array $options) {
        // Info perso
        $builder->add('civility', 'choice',
            [
                'choices'   => $options['civilities'],
                'required'  => false,
                'label' => $this->translator->trans('Civility')
            ]
        );
        
        $builder->add('company', 'text', array(
            'required' => FALSE,
            'label' => $this->translator->trans('Company')));

        $builder->add('firstName', 'text', array(
            'required' => TRUE,
            'label' => $this->translator->trans('First name')));

        $builder->add('lastName', 'text', array(
            'required' => TRUE,
            'label' => $this->translator->trans('Last name')));

        $builder->add('phone', 'text', array(
            'required' => FALSE,
            'label' => $this->translator->trans('Phone')));

        $builder->add('email', 'repeated', array(
            'type' => 'email',
            'invalid_message' => $this->translator->trans('The email fields must match.'),
            'options' => array(
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => "xxx@yyyy.fr"
                    )),
            'required' => true,
            'first_options'  => array('label' => $this->translator->trans('Email')),
            'second_options' => array('label' => $this->translator->trans('Repeat Email')),
            ));

        //Address
        $builder->add('addressStreet', 'text', array(
            'required'  => true,
            'label' => $this->translator->trans('Address')
            ));

        $builder->add('addressPb', 'text', array(
            'required'  => false,
            'label' => $this->translator->trans('Locality, post box')
            ));

        $builder->add('addressLiving', 'text', array(
            'required'  => false,
            'label' => $this->translator->trans('Living with')
            ));

        $builder->add('addressExtra', 'text', array(
            'required'  => false,
            'label' => $this->translator->trans('Apartment, floor numbers')
            ));

        $builder->add('addressZipcode', 'number', array(
            'required'  => true,
            'label' => $this->translator->trans('Zipcode')
            ));

        $builder->add('addressCity', 'text', array(
            'required'  => true,
            'label' => $this->translator->trans('City')
            ));

        $builder->add('addressCountry', 'country', array(
            'required'  => true,
            'preferred_choices' => array('FR'),
            'data' => 'FR',
            'label' => $this->translator->trans('Country')
            ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {        
        $tunnels = $this->paymentMethodsToTunnels($options['payment_methods']);

        // equivalences for each tunnels subform
        $tunnelForm = $builder->create('tunnels', 'form', array('virtual' => true, 'label' => false));

        foreach(array_keys($tunnels) as $key) {
            $this->buildAmountSelectorSubForm($tunnelForm, $key, $options);
        }

        $builder->add($tunnelForm);
        
        $this->buildPersonnalDetails($builder, $options);

        $builder->add('erf', 'choice', [
            'choices'   => [
                0 => $this->translator->trans('by email'),
                1 => $this->translator->trans('by post')
            ],
            'required'  => true,
            'expanded' => true,
            'multiple' => false,
            'label' => $this->translator->trans('I prefer to receive my tax receipt'),
            'data' => 0,
            'mapped' => false,
            ]
        );

        $builder->add('optin', 'checkbox', [
            'required' => false,
            'label' => $this->translator->trans('I agree to receive informations from Association XY'),
        ]);

        // payment methods for each tunnels subform
        $pmForm = $builder->create('payment_method', 'form', ['virtual' => true, 'label' => false]);

        foreach($options['payment_methods'] as $pm) {
            $pmForm->add($pm->getId(), 'submit', [
                    'label'         => $pm->getName(),
                    'attr'          => ['class'=> 'btn btn-primary tunnel-' . $pm->getTunnel()], //used in the JS part                                        
                ]);
        }

        $builder->add($pmForm);
    }

    public function getName()
    {
        return 'donate';
    }

    protected function getEquivalencesOptions($equivalences, $tunnel = PaymentMethodInterface::TUNNEL_SPOT)
    {

        $options = [];

        foreach ($equivalences[$tunnel] as $equivalence) {
            $options[$equivalence->getAmount()] = $equivalence->getLabel();
        }
        $options['manual'] = $this->translator->trans('Other amount');

        return $options;
    }


    /**
     * transform a list of payment methods to an array with key is tunnel name
     * @param  array $paymentMethods a list of PaymentMethodInterface
     * @return [type]                 [description]
     */
    protected function paymentMethodsToTunnels($paymentMethods) {
        $tunnels = [];
        foreach($paymentMethods as $pm) {
            $tunnels[$pm->getTunnel()][] = $pm;
        }

        return $tunnels;
    }
    /**
    * {@inheritdoc}
    */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection'   => false,
            'civilities' => [],
            'equivalences' => [],
            'payment_methods' => [],
            'min_amount' => 5,
            'max_amount' => 4000,
        ]);
    }   
}
