<?php

namespace Ecedi\Donate\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Une classe pour le formulaire des comptes utilisateurs
 */
class AccountType extends AbstractType
{
    private $roles;

    private $route;

    /**
     * Construction du formulaire
     *
     * @see Symfony\Component\Form.AbstractType::buildForm()
     */
    public function __construct($roles, $route)
    {
        $this->roles = $roles;
        $this->route = $route;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text', array(
                'label'             => "Username",
                 'required'          => true,
            ))
            ->add('email', 'text', array(
                'label'             => "Email",
                'required'          => true,
            ));
        $builder
            ->add('roles', 'choice', array(
                'choices'           => $this->roles,
                'required'          => true,
                'multiple'          => true,
                'expanded'          => true,
            ))
            ->add('enabled', 'choice', array(
                'choices'           => array("No", "Yes"),
                'required'          => true,
                'multiple'          => false,
                'expanded'          => true,
                'label'             => 'Enabled',
            ))
             ->add('submit_save', 'submit', array(
                'label'     => 'Submit',
            ));
            // gestion des champs différents selon le type de formulaire (edition ou création)
            // TODO revoir ce code, on ne devrait pas dépendre de la la route
            if ($this->route == 'donate_admin_user_new') {
                $builder
                    ->add('password', 'repeated', array(
                        'type'              => 'password',
                        'invalid_message'   => "Passwords don't match",
                        'first_name'        => "Mot_de_passe",
                        'second_name'       => "Confirmation_mot_de_passe",
                        'options'           => array(),
                    ));
            } else {
                $builder
                    ->add('submit_delete', 'submit', array(
                        'label'     => 'Delete',
                    ));
            }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'forms',
        ));
    }
    /**
     * Get name
     *
     * @see Symfony\Component\Form.FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'donate_admin_account_new';
    }
}
