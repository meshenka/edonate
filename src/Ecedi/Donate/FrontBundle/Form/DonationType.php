<?php
namespace Ecedi\Donate\FrontBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DonationType extends AbstractType
{

  private $params;

  private $container;

  /**
   * params
   *
   * @return array parameters
   */
  public function getParams()
  {
    return $this->Params;
  }

  /**
   * [Description]
   *
   * @param Array $newParams Parameters
   */
  protected function setParams($Params)
  {
    $this->Params = $Params;

    return $this;
  }


  public function __construct(Container $container)
  {
    $this->container = $container;

    $params = []; //DonationType parameters, from bundle settings

    $params['civility'] = $this->container->getParameter('donate_front.form.civility');

    $paymentMethodDiscovery = $this->container->get('donate_core.payment_method_discovery');
    
    $params['payment_methods'] = $paymentMethodDiscovery->getEnabledMethods();

    $params['equivalences'] = $this->container->get('donate_core.equivalence.factory')->get();

    $this->setParams($params);
  }

  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    // Configurations des montants de dons
    $minAmount = 5;
    $maxAmount = 4000;

    $params = $this->getParams();

    $builder->add('amount_preselected', 'choice',
      array(
        'choices'   => $this->getEquivalencesOptions(),
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
              'min' => $minAmount,
              'max' => $maxAmount,
              'minMessage' => $this->container->get('translator')->trans('Amount must be greater than ') . $minAmount,
              'maxMessage' => $this->container->get('translator')->trans('Amount must be lower than ') . $maxAmount,
            )
          )
        )
      )
    );

        // Info perso
    $builder->add('civility', 'choice',
     array(
       'choices'   => $params['civility'],
       'required'  => false,
        'label' => $this->container->get('translator')->trans('Civility')
       )
     );
    $builder->add('company', 'text', array(
     'required' => FALSE,
     'label' => $this->container->get('translator')->trans('Company')));

    $builder->add('firstName', 'text', array(
     'required' => TRUE,
     'label' => $this->container->get('translator')->trans('First name')));

    $builder->add('lastName', 'text', array(
     'required' => TRUE,
     'label' => $this->container->get('translator')->trans('Last name')));

    $builder->add('phone', 'text', array(
     'required' => FALSE,
     'label' => $this->container->get('translator')->trans('Phone')));

    $builder->add('email', 'repeated', array(
      'type' => 'email',
      'invalid_message' => $this->container->get('translator')->trans('The email fields must match.'),
      'options' => array(
       'attr' => array(
        'class' => 'form-control',
        'placeholder' => "xxx@yyyy.fr"
        )),
      'required' => true,
      'first_options'  => array('label' => $this->container->get('translator')->trans('Email')),
      'second_options' => array('label' => $this->container->get('translator')->trans('Repeat Email')),
      ));

        //Address
    $builder->add('addressStreet', 'text', array(
     'required'  => true,
     'label' => $this->container->get('translator')->trans('Address')
     ));

    $builder->add('addressPb', 'text', array(
     'required'  => false,
     'label' => $this->container->get('translator')->trans('Locality, post box')
     ));

    $builder->add('addressLiving', 'text', array(
     'required'  => false,
     'label' => $this->container->get('translator')->trans('Living with')
     ));

    $builder->add('addressExtra', 'text', array(
     'required'  => false,
     'label' => $this->container->get('translator')->trans('Apartment, floor numbers')
     ));

    $builder->add('addressZipcode', 'number', array(
     'required'  => true,
     'label' => $this->container->get('translator')->trans('Zipcode')
     ));

    $builder->add('addressCity', 'text', array(
     'required'  => true,
     'label' => $this->container->get('translator')->trans('City')
     ));

    $builder->add('addressCountry', 'country', array(
     'required'  => true,
     'preferred_choices' => array('FR'),
     'data' => 'FR',
     'label' => $this->container->get('translator')->trans('Country')
     ));

    $builder->add('erf', 'choice', array(
     'choices'   => array(
        0 => $this->container->get('translator')->trans('by email'),
        1 => $this->container->get('translator')->trans('by post')
      ),
     'required'  => true,
     'expanded' => true,
     'multiple' => false,
     'label' => $this->container->get('translator')->trans('I prefer to receive my tax receipt'),
     'data' => 0,
     'mapped' => false,
     ));
    $builder->add('optin', 'checkbox', array(
     'required' => false,
     'label' => $this->container->get('translator')->trans('I agree to receive informations from Association XY'),
     ));

    //payment method
    $methods = $params['payment_methods'];

    if (sizeof($methods) == 1) {
      //hidden input
      $builder->add('payment_method', 'hidden', array(
        'required' => true,
        'data' => array_keys($methods)[0],
        'mapped' => false,
        ));
    } else {

      //ld($params['payment_methods']);
      //
      $choices = array();
      foreach($params['payment_methods'] as $id => $pm) {
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

  protected function getEquivalencesOptions()
  {

    $options = [];

    $params = $this->getParams();
    foreach ($params['equivalences'] as $equivalence) {
      $options[$equivalence->getAmount()] = $equivalence->getLabel();
    }
    $options['manual'] = $this->container->get('translator')->trans('Other amount');

    return $options;
  }

  /**
 * {@inheritdoc}
 */
public function setDefaultOptions(OptionsResolverInterface $resolver)
{
    $resolver->setDefaults(array(
        'csrf_protection'   => false,
    ));
}
}
