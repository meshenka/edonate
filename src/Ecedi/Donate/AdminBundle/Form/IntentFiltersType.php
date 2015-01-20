<?php

namespace Ecedi\Donate\AdminBundle\Form;

use Ecedi\Donate\CoreBundle\Entity\Intent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class pour le formulaire de filtres des intents (dons en cours)
 */
class IntentFiltersType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $types = Intent::getTypesLabel();
        $status = Intent::getStatusLabel();

        $builder
            ->add('type', 'choice', array(
                'label'         => "Donation types",
                'choices'       => $types,
                'required'      => true,
                'multiple'      => true,
                'expanded'      => true,
                'data'          => array_keys($types),
                'translation_domain' => 'forms',
            ))
            ->add('status', 'choice', array(
                'label'         => "Status",
                'choices'       => $status,
                'required'      => true,
                'multiple'      => true,
                'expanded'      => true,
                'data'          => array_keys($status),
            ))
            ->add('email', 'text', array(
                'label'         => "Email",
                'required'      => false,
            ))
            ->add('minAmount', 'text', array(
                'label'         => "Min amount",
                'required'      => false,
            ))
            ->add('maxAmount', 'text', array(
                'label'         => "Max amount",
                'required'      => false,
            ))
            ->add('minCreatedAt', 'date', array(
                'label'         => 'Donation submitted', //Dons soumis
                'input'         => 'datetime',
                'widget'        => 'single_text',
                'format'        => "dd/MM/yyyy",
                'required'      => false,
            ))
            ->add('maxCreatedAt', 'date', array(
                'input'         => 'datetime',
                'widget'        => 'single_text',
                'format'        => "dd/MM/yyyy",
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
            'translation_domain'    => 'forms',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'intent_filters';
    }
}
