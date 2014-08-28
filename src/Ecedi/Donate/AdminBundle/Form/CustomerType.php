<?php

namespace Ecedi\Donate\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Une classe pour éditer un donateur
 */

class CustomerType extends AbstractType
{
    private $container;

    /*public function __construct(Container $container)
    {
        $this->container = $container;
    }*/

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //$params['civility'] = $this->container->getParameter('donate_front.form.civility');

        /*$builder->add('civility', 'choice', array(
            'choices'   => $params['civility'],
            'required'  => false,
            'label'     => 'Civility'
        ));*/
        $builder
            ->add('company', 'text', array(
                'required'  => FALSE,
                'label'     => 'Company'
            ))
            ->add('firstName', 'text', array(
                'required'  => TRUE,
                'label'     => 'Firstname'
            ))
            ->add('lastName', 'text', array(
                'required'  => TRUE,
                'label'     => 'Lastname'
            ))
            ->add('phone', 'text', array(
                'required'  => FALSE,
                'label'     => 'Phone'
            ))
            ->add('email', 'email', array(
                'required'  => true,
                'label'     => 'Email'
            ))
            ->add('addressStreet', 'text', array(
                'required'  => true,
                'label'     => 'Address'
            ))
            ->add('addressPb', 'text', array(
                'required'  => false,
                'label'     => 'Locality, post box'
            ))
            ->add('addressLiving', 'text', array(
                'required'  => false,
                'label'     => 'Living with'
            ))
            ->add('addressExtra', 'text', array(
                'required'  => false,
                'label'     => 'Apartment, floor numbers'
            ))
            ->add('addressZipcode', 'number', array(
                'required'  => true,
                'label'     => 'Zipcode'
            ))
            ->add('addressCity', 'text', array(
                'required'  => true,
                'label'     => 'City'
            ))
            ->add('addressCountry', 'country', array(
                'required'  => true,
                'label'     => 'Country'
            ))
            ->add('submit', 'submit', array(
                'label'     => 'Submit',
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'            => 'Ecedi\Donate\CoreBundle\Entity\Customer',
            'translation_domain'    => 'forms'
        ));
    }
    /**
     * Get name
     *
     * @see Symfony\Component\Form.FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'donate_admin_customer_edit';
    }

}
