<?php
namespace Ecedi\Donate\FrontBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
// use Symfony\Component\DependencyInjection\Container;
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

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       
        $builder->add('amount_preselected', 'choice',
            array(
                'choices'   => $this->getEquivalencesOptions($options['equivalences']),
                'required'  => true,
                'expanded' => true,
                'multiple' => false,
                'label' => false,
                'data' => 100,
                'mapped' => false,
                )
            );

        $builder->add('amount_manual', 'money',
            array(
                'currency' => 'EUR',
                'required'  => false,
                'label' => false,
                'precision' => 0,
                'mapped' => false,
                'constraints' => array(
                    new Assert\Range(
                        array(
                            'min' => $options['min_amount'],
                            'max' => $options['max_amount'],
                            'minMessage' => $this->translator->trans('Amount must be greater than ') . $options['min_amount'],
                            'maxMessage' => $this->translator->trans('Amount must be lower than ') . $options['max_amount'],
                            )
                        )
                    )
                )
            );

    // Info perso
        $builder->add('civility', 'choice',
            array(
                'choices'   => $options['civilities'],
                'required'  => false,
                'label' => $this->translator->trans('Civility')
                )
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

        $builder->add('erf', 'choice', array(
            'choices'   => array(
                0 => $this->translator->trans('by email'),
                1 => $this->translator->trans('by post')
                ),
            'required'  => true,
            'expanded' => true,
            'multiple' => false,
            'label' => $this->translator->trans('I prefer to receive my tax receipt'),
            'data' => 0,
            'mapped' => false,
            ));
        $builder->add('optin', 'checkbox', array(
            'required' => false,
            'label' => $this->translator->trans('I agree to receive informations from Association XY'),
            ));

    //payment method
        $methods = $options['payment_methods'];

        if (sizeof($methods) == 1) {
    //hidden input
            $builder->add('payment_method', 'hidden', array(
                'required' => true,
                'data' => array_keys($methods)[0],
                'mapped' => false,
                ));
        } else {

            $choices = array();
            foreach ($methods as $id => $pm) {
                $choices[$id] = $pm->getName();
            }

    //radio button
            $builder->add('payment_method', 'choice', array(
                'choices'   => $choices,
                'required'  => true,
                'expanded' => true,
                'multiple' => false,
    //'data' => reset(array_keys($methods)),
                'label' => false,
                'mapped' => false,
                ));
        }
    }

    public function getName()
    {
        return 'donate';
    }

    protected function getEquivalencesOptions($equivalences)
    {

        $options = [];

        foreach ($equivalences[PaymentMethodInterface::TUNNEL_SPOT] as $equivalence) {
            $options[$equivalence->getAmount()] = $equivalence->getLabel();
        }
        $options['manual'] = $this->translator->trans('Other amount');

        return $options;
    }

    /**
    * {@inheritdoc}
    */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
            'civilities' => array(),
            'equivalences' => array(),
            'payment_methods' => array(),
            'min_amount' => 5,
            'max_amount' => 4000,
        ));
    }   
}
