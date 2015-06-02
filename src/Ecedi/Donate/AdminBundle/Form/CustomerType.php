<?php

namespace Ecedi\Donate\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Une classe pour éditer un donateur
 */

class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('company', 'text', array(
                'required'  => FALSE,
                'label'     => 'Company',
            ))
            ->add('firstName', 'text', array(
                'required'  => TRUE,
                'label'     => 'Firstname',
            ))
            ->add('lastName', 'text', array(
                'required'  => TRUE,
                'label'     => 'Lastname',
            ))
            ->add('phone', 'text', array(
                'required'  => FALSE,
                'label'     => 'Phone',
            ))
            ->add('email', 'email', array(
                'required'  => true,
                'label'     => 'Email',
            ))
            ->add('addressStreet', 'text', array(
                'required'  => true,
                'label'     => 'Address',
            ))
            ->add('addressPb', 'text', array(
                'required'  => false,
                'label'     => 'Locality, post box',
            ))
            ->add('addressLiving', 'text', array(
                'required'  => false,
                'label'     => 'Living with',
            ))
            ->add('addressExtra', 'text', array(
                'required'  => false,
                'label'     => 'Apartment, floor numbers',
            ))
            ->add('addressZipcode', 'number', array(
                'required'  => true,
                'label'     => 'Zipcode',
            ))
            ->add('addressCity', 'text', array(
                'required'  => true,
                'label'     => 'City',
            ))
            ->add('addressCountry', 'country', array(
                'required'  => true,
                'label'     => 'Country',
            ))
            ->add('submit', 'submit', array(
                'label'     => 'Submit',
            ));
    }

    /**
     * {@inheritdoc}
     * @since 3.1 use new method signatire since sf 2.7
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'            => 'Ecedi\Donate\CoreBundle\Entity\Customer',
            'translation_domain'    => 'forms',
        ));
    }
    /**
     * Get name
     *
     * @see Symfony\Component\Form.FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'customer';
    }
}
