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
use Ecedi\Donate\CoreBundle\Entity\Layout;
/**
 * Une classe pour le formulaire des comptes utilisateurs
 */
class LayoutType extends AbstractType
{
    /**
     * @since 2.4 convert i18n options to a usable ChoiceType choices
     *
     * @param  array $languages languages as extracted from config donate_front.i18n
     * @return array key is the Label, value is the language code
     */
    protected function languagesToOptions($languages)
    {
        return  array_combine($languages, $languages);
    }

    /**
     * {@inheritdoc}
     * @since 2.4 flip keys and values and add choices_as_values option
     * @param  FormBuilderInterface $builder [description]
     * @param  array                $options [description]
     * @return [type]               [description]
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array(
                'required'  => true,
                'label'     => 'Nom',
            ));

        $builder->add('language', 'choice', array(
            'label'     => 'Langue',
            'choices' => $this->languagesToOptions($options['language']),
            'required' => true,
            'empty_value' => false,
            'expanded' => false,
            'multiple' => false,
            'choices_as_values' => true,
        ));

        $builder->add('skin', 'choice', array(
                'choices'           => $options['skins'],
                'required'          => true,
                'label'             => 'Theme',
                'choices_as_values' => true,
            ));

        $builder->add('baseline', 'text', array(
                'required'          => true,
                'label'          => 'Baseline',
            ));

        $builder->add('meta_title', 'text', array(
                'required'          => true,
                'label'          => 'Meta Title',
            ));

        $builder->add('meta_description', 'text', array(
                'required'          => true,
                'label'          => 'Meta Description',
            ));

        $builder->add('meta_keywords', 'text', array(
                'required'          => true,
                'label'          => 'Meta Keywords',
            ));

        $builder->add('logo', 'file', array(
                'required'          => false,
                'label'          => 'Logo',
            ));

        $builder->add('logoAlt', 'text', array(
                'required'          => false,
                'label'          => 'Texte alternatif du logo',
            ));

        $builder->add('logoUrl', 'url', array(
                'required'          => false,
                'label'          => 'Url du logo',
            ));

        $builder->add('logoTitle', 'text', array(
                'required'          => false,
                'label'          => 'Titre du lien sur le logo',
            ));

        $builder->add('background', 'file', array(
                'required'          => false,
                'label'          => 'Background',
            ));

        $builder->add('submit', 'submit', array(
                'label'     => 'Valider',
            ));
    }

    /**
     * {@inheritdoc}
     * @since 2.4 use new method signatire since sf 2.7
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'forms',
            'data_class' => 'Ecedi\Donate\CoreBundle\Entity\Layout',
            'language' => [],
            'skins' => array(
                    Layout::SKIN_DEFAULT => Layout::SKIN_DEFAULT,
                    Layout::SKIN_CUSTOM => Layout::SKIN_CUSTOM,
                    Layout::SKIN_LIGHT => Layout::SKIN_LIGHT,
                    Layout::SKIN_DARK => Layout::SKIN_DARK,
                ),
        ));
    }
    /**
     * Get name
     *
     * @see Symfony\Component\Form.FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'layout';
    }
}
