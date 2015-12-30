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
use Ecedi\Donate\CoreBundle\Entity\Block;

/**
 * Une classe pour le formulaire des comptes utilisateurs
 * @since 2.4 flip keys and values and add choices_as_values option
 * @since 2.4 use placeholder instead of empty_value. see  http://symfony.com/doc/current/reference/forms/types/choice.html#placeholder
 */
class BlockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'disabled'  => true,
                'required'  => true,
                'label'     => 'Nom machine',
            ));

        //@since 2.4 flip keys and values and add choices_as_values option
        $builder->add('enabled', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'choices'           => array('No' => 0, 'Yes' => 1),
                'required'          => true,
                'label'             => 'Activé',
                'choices_as_values' => true,
            ));

        $builder->add('position', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required'          => true,
                'label'          => 'Position',
            ));

        $builder->add('type', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required'          => true,
                'disabled'           => true,
                'label'          => 'Type',
            ));

        $builder->add('title', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required'  => false,
                'label'     => 'Titre',
            ));

        $builder->add('title_url', 'Symfony\Component\Form\Extension\Core\Type\UrlType', array(
            'required'  => false,
            'label'     => 'Lien du Titre',

        ));

        $builder->add('title_url_title', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
            'required'  => false,
            'label'     => 'Titre du lien',

        ));

        $builder->add('body', 'Trsteel\CkeditorBundle\Form\Type\CkeditorType', array(
                'required'  => false,
                'label'     => 'Contenu',
                'attr' => array(
                    'class' => 'editable',
                ),
                'transformers' => array('html_purifier'),

            ));

        // @since 2.4 flip keys and values and add choices_as_values option
        $builder->add('format', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'label'     => 'Format',
                'choices' => $options['body_formats'],
                'required' => true,
                'preferred_choices' => array(Block::FORMAT_HTML),
                'placeholder' => false,
                'expanded' => false,
                'multiple' => false,
                'choices_as_values' => true,
            ));

        $builder->add('submit', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
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
            'body_formats' => [
                'HTML' => Block::FORMAT_HTML,
                'Markdown' => Block::FORMAT_MARKDOWN,
                'Brut' => Block::FORMAT_RAW,
            ],
        ));
    }
}
