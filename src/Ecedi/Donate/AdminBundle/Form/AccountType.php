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
 * Une classe pour le formulaire des comptes utilisateurs
 * @since 2.3  class no more use constructor argument, we switch to options
 */

class AccountType extends AbstractType
{
    /**
     *
     * @since 2.3 class no more use constructor argument, we switch to options
     * @since 2.4 flip keys and values and add choices_as_values option
     * @param FormBuilderInterface $builder [description]
     * @param array                $options [description]
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label'             => 'Username',
                'required'          => true,
            ))
            ->add('email', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label'             => "Email",
                'required'          => true,
            ))
            ->add('roles', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'choices'           => $options['roles'],
                'required'          => true,
                'multiple'          => true,
                'expanded'          => true,
                'choices_as_values' => true,
            ))
            ->add('enabled', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'choices'           => ['No' => false, 'Yes' => true],
                'required'          => true,
                'multiple'          => false,
                'expanded'          => true,
                'label'             => 'Enabled',
                'choices_as_values' => true,
            ))
             ->add('submit_save', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label'     => 'Submit',
            ));

            // gestion des champs différents selon le type de formulaire (edition ou création)
            // @since 2.3  we use option 'action' instead of _route to dedure form fields
            if ($options['action'] == 'new') {
                $builder
                    ->add('password', 'Symfony\Component\Form\Extension\Core\Type\RepeatedType', array(
                        'type'              => 'Symfony\Component\Form\Extension\Core\Type\PasswordType',
                        'invalid_message'   => "Passwords don't match",
                        'first_name'        => 'Mot_de_passe',
                        'second_name'       => 'Confirmation_mot_de_passe',
                        'options'           => array(),
                    ));

                return;
            }

        $builder
                ->add('submit_delete', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                    'label'     => 'Delete',
                ));
    }

    /**
     * default form options
     * @since 2.3  we use options roles and action instead of constructor arguments
     * @since 2.4 use new method signatire since sf 2.7
     * @param OptionsResolver $resolver [description]
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'forms',
            'roles' => array('ROLE_USER' => 'ROLE_USER'),
            'action' => 'new', //or edit
        ));
    }
}
