<?php

namespace Ecedi\Donate\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class IntentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type')
            ->add('amount')
            ->add('currency')
            ->add('status')
            ->add('paymentMethod')
            ->add('campaign')
            ->add('fiscal_receipt')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => 'Ecedi\Donate\CoreBundle\Entity\Intent',
            'csrf_protection'   => false,   // Redondant avec la sécurisation de l'API REST
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'intent';    // Ne pas modifier le nom du formulaire !
    }
}
