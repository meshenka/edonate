<?php

namespace Ecedi\Donate\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
     * {@inheritdoc}
     * @since 2.4 use new method signatire since sf 2.7
     */
    public function configureOptions(OptionsResolver $resolver)
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
