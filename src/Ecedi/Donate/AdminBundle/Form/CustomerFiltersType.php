<?php
/**
 * @author Sylvain Gogel <sgogel@ecedi.fr>
 * @copyright Agence Ecedi (c) 2015
 * @package eDonate
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace Ecedi\Donate\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ->add('email', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label'         => "Email",
                'required'      => false,
            ))
            ->add('lastName', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label'         => "Name",
                'required'      => false,
            ))
            ->add('addressZipcode', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label'         => "ZipCode",
                'required'      => false,
            ))
            ->add('addressCity', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label'         => "City",
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
