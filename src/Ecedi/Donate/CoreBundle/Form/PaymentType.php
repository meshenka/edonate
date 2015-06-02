<?php

namespace Ecedi\Donate\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaymentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('status')
            ->add('responseCode')
            ->add('transaction')
            ->add('autorisation')
            ->add('response')
            ->add('alias')
        ;
    }

    /**
     * {@inheritdoc}
     * @since 3.1 use new method signatire since sf 2.7
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => 'Ecedi\Donate\CoreBundle\Entity\Payment',
            'csrf_protection'   => false,   // Redondant avec la sécurisation de l'API REST
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'payment';   // Ne pas modifier le nom du formulaire !
    }
}
