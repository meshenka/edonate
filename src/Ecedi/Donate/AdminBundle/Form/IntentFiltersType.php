<?php
/**
 * @author Sylvain Gogel <sgogel@ecedi.fr>
 * @copyright Agence Ecedi (c) 2015
 * @package eDonate
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace Ecedi\Donate\AdminBundle\Form;

use Ecedi\Donate\CoreBundle\Entity\Intent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class pour le formulaire de filtres des intents (dons en cours)
 */
class IntentFiltersType extends AbstractType
{
    /**
     * {@inheritdoc}
     * @since 2.4 flip keys and values and add choices_as_values option
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $types = array_flip(Intent::getTypesLabel());
        $status = array_flip(Intent::getStatusLabel());

        $builder
            ->add('type', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'label'         => 'Donation types',
                'choices'       => $types,
                'required'      => true,
                'multiple'      => true,
                'expanded'      => true,
                'data'          => array_values($types),
                'translation_domain' => 'forms',
                'choices_as_values' => true,
            ))
            ->add('status', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'label'         => 'Status',
                'choices'       => $status,
                'required'      => true,
                'multiple'      => true,
                'expanded'      => true,
                'data'          => array_values($status),
                'choices_as_values' => true,
            ))
            ->add('email', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label'         => "Email",
                'required'      => false,
            ))
            ->add('minAmount', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label'         => "Min amount",
                'required'      => false,
            ))
            ->add('maxAmount', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label'         => "Max amount",
                'required'      => false,
            ))
            ->add('minCreatedAt', 'Symfony\Component\Form\Extension\Core\Type\DateType', array(
                'label'         => 'Donation submitted', //Dons soumis
                'input'         => 'datetime',
                'widget'        => 'single_text',
                'format'        => "dd/MM/yyyy",
                'required'      => false,
            ))
            ->add('maxCreatedAt', 'Symfony\Component\Form\Extension\Core\Type\DateType', array(
                'input'         => 'datetime',
                'widget'        => 'single_text',
                'format'        => "dd/MM/yyyy",
                'required'      => false,
            ))
            ->add('submit_filter', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label'         => 'Filter',
            ))
            ->add('submit_export', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label'         => 'Export',
            ));
    }

    /**
     * {@inheritdoc}
     * @since 2.4 use new method signatire since sf 2.7
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'       => false,
            'translation_domain'    => 'forms',
        ));
    }
}
