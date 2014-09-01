<?php

namespace Ecedi\Donate\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
//use Ecedi\Donate\CoreBundle\Entity\Block;

/**
 * Une classe pour le formulaire des comptes utilisateurs
 */
class EquivalenceType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('amount', 'text', array(
                'required'  => true,
                'label'     => 'montant',
            ));

        $builder->add('label', 'text', array(
                'required'  => true,
                'label'     => 'label',
            ));
        

        $builder->add('currency', 'text', array(
                'required'          => true,
                'label'          => 'Currency',
            ));

        $builder->add('submit', 'submit', array(
                'label'     => 'Valider',
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'forms',
            'data_class' => 'Ecedi\Donate\CoreBundle\Entity\Equivalence'
        ));
    }
    /**
     * Get name
     *
     * @see Symfony\Component\Form.FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'equivalence';
    }
}
