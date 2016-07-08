<?php

namespace Ecedi\Donate\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('remoteId')
            ->add('civility')
            ->add('firstName')
            ->add('lastName')
            ->add('middleName')
            ->add('email')
            ->add('birthday', 'date')
            ->add('phone')
            ->add('company')
            ->add('website')
            ->add('addressNber')
            ->add('addressStreet')
            ->add('addressExtra')
            ->add('addressPb')
            ->add('addressZipcode')
            ->add('addressCity')
            ->add('addressCountry')
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @since 2.4 use new method signatire since sf 2.7
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ecedi\Donate\CoreBundle\Entity\Customer',
            'csrf_protection' => false,   // Redondant avec la sécurisation de l'API REST
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'customer';  // Ne pas modifier le nom du formulaire !
    }
}
