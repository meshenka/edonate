<?php
/**
 * @author  Sylvain Gogel <sgogel@ecedi.fr>
 * @copyright 2015 Ecedi
 * @package eCollecte
 *
 */
namespace Ecedi\Donate\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Une classe pour le formulaire des comptes utilisateurs
 */
class AffectationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('code', 'text', array(
                'required'  => true,
                'label'     => 'Code',
            ));

        $builder->add('label', 'text', array(
                'required'          => true,
                'label'          => 'Label',
            ));

        $builder->add('submit', 'submit', array(
                'label'     => 'Submit',
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'forms',
            'data_class' => 'Ecedi\Donate\CoreBundle\Entity\Affectation',
        ));
    }

    /**
     * Get name
     *
     * @see Symfony\Component\Form.FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'affectation';
    }
}
