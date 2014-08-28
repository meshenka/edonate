<?php

namespace Ecedi\Donate\AdminBundle\Form;

use  Ecedi\Donate\CoreBundle\Entity\Customer;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class pour le formulaire de filtres des customers (donateurs)
 */
class CustomerFiltersType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'text', array(
                'label'         => "Email",
                'required'      => false,
            ))
            ->add('lastName', 'text', array(
                'label'         => "Name",
                'required'      => false,
            ))
            ->add('addressZipcode', 'text', array(
                'label'         => "ZipCode",
                'required'      => false,
            ))
            ->add('addressCity', 'text', array(
                'label'         => "City",
                'required'      => false,
            ))
            ->add('submit_filter', 'submit', array(
                'label'         => 'Filter',
            ))
            ->add('submit_export', 'submit', array(
                'label'         => 'Export',
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'       => false,
            'translation_domain'    => 'forms'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'customer_filters';
    }
}
