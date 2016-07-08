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
 * Une classe pour éditer un donateur
 */

class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('company', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required'  => FALSE,
                'label'     => 'Company',
            ))
            ->add('firstName', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required'  => TRUE,
                'label'     => 'Firstname',
            ))
            ->add('lastName', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required'  => TRUE,
                'label'     => 'Lastname',
            ))
            ->add('phone', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required'  => FALSE,
                'label'     => 'Phone',
            ))
            ->add('email', 'Symfony\Component\Form\Extension\Core\Type\EmailType', array(
                'required'  => true,
                'label'     => 'Email',
            ))
            ->add('addressStreet', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required'  => true,
                'label'     => 'Address',
            ))
            ->add('addressPb', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required'  => false,
                'label'     => 'Locality, post box',
            ))
            ->add('addressLiving', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required'  => false,
                'label'     => 'Living with',
            ))
            ->add('addressExtra', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required'  => false,
                'label'     => 'Apartment, floor numbers',
            ))
            ->add('addressZipcode', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'required'  => true,
                'label'     => 'Zipcode',
            ))
            ->add('addressCity', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required'  => true,
                'label'     => 'City',
            ))
            ->add('addressCountry', 'Symfony\Component\Form\Extension\Core\Type\CountryType', array(
                'required'  => true,
                'label'     => 'Country',
            ))
            ->add('submit', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label'     => 'Submit',
            ));
    }

    /**
     * {@inheritdoc}
     * @since 2.4 use new method signatire since sf 2.7
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'            => 'Ecedi\Donate\CoreBundle\Entity\Customer',
            'translation_domain'    => 'forms',
        ));
    }
}
