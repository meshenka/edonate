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
                'choices'           => $options['roles'],
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
            if ($options['action'] == 'new') {
                $builder
                    ->add('password', 'repeated', array(
                        'type'              => 'password',
                        'invalid_message'   => "Passwords don't match",
                        'first_name'        => "Mot_de_passe",
                        'second_name'       => "Confirmation_mot_de_passe",
                        'options'           => array(),
                    ));

                return;
            }

        $builder
                ->add('submit_delete', 'submit', array(
                    'label'     => 'Delete',
                ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'forms',
            'roles' => array('ROLE_USER' => 'ROLE_USER'),
            'action' => 'new', //or edit
        ));
    }
    /**
     * Get name
     *
     * @see Symfony\Component\Form.FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'ecollect_account';
    }
}
