<?php

namespace Ecedi\Donate\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Ecedi\Donate\CoreBundle\Entity\Block;

/**
 * Une classe pour le formulaire des comptes utilisateurs
 * @since 2.4 flip keys and values and add choices_as_values option
 */
class BlockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array(
                'disabled'  => true,
                'required'  => true,
                'label'     => 'Nom machine',
            ));

        //@since 2.4 flip keys and values and add choices_as_values option
        $builder->add('enabled', 'choice', array(
                'choices'           => array('No' => 0, 'Yes' => 1),
                'required'          => true,
                'label'             => 'Activé',
                'choices_as_values' => true,
            ));

        $builder->add('position', 'text', array(
                'required'          => true,
                'label'          => 'Position',
            ));

        $builder->add('type', 'text', array(
                'required'          => true,
                'disabled'           => true,
                'label'          => 'Type',
            ));

        $builder->add('title', 'text', array(
                'required'  => false,
                'label'     => 'Titre',
            ));

        $builder->add('title_url', 'url', array(
            'required'  => false,
            'label'     => 'Lien du Titre',

        ));

        $builder->add('title_url_title', 'text', array(
            'required'  => false,
            'label'     => 'Titre du lien',

        ));

        $builder->add('body', 'ckeditor', array(
                'required'  => false,
                'label'     => 'Contenu',
                'attr' => array(
                    'class' => 'editable',
                ),
                'transformers' => array('html_purifier'),

            ));

        // @since 2.4 flip keys and values and add choices_as_values option
        $builder->add('format', 'choice', array(
                'label'     => 'Format',
                'choices' => [
                    'HTML' => Block::FORMAT_HTML,
                    'Markdown' => Block::FORMAT_MARKDOWN,
                    'Brut' => Block::FORMAT_RAW,
                ],
                'required' => true,
                //'preferred_choices' => array(Block::FORMAT_HTML),
                'empty_value' => false,
                'expanded' => false,
                'multiple' => false,
                'choices_as_values' => true,
            ));

        $builder->add('submit', 'submit', array(
                'label'     => 'Valider',
            ));
    }

    /**
     * @since 2.4 use new method signatire since sf 2.7
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'forms',
            'data_class' => 'Ecedi\Donate\CoreBundle\Entity\Block',
        ));
    }
    /**
     * Get name
     *
     * @see Symfony\Component\Form.FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'block';
    }
}
