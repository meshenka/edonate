<?php
/**
 * @author Sylvain Gogel <sgogel@ecedi.fr>
 * @copyright Agence Ecedi (c) 2015
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ecedi\Donate\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Ecedi\Donate\CoreBundle\Entity\Layout;

/**
 * Une classe pour le formulaire des comptes utilisateurs.
 */
class LayoutType extends AbstractType
{
    /**
     * @since 2.4 convert i18n options to a usable ChoiceType choices
     *
     * @param array $languages languages as extracted from config donate_front.i18n
     *
     * @return array key is the Label, value is the language code
     */
    protected function languagesToOptions($languages)
    {
        return  array_combine($languages, $languages);
    }

    /**
     * {@inheritdoc}
     *
     * @since 2.4 flip keys and values and add choices_as_values option
     * @since 2.4 use placeholder instead of empty_value. see  http://symfony.com/doc/current/reference/forms/types/choice.html#placeholder
     *
     * @param FormBuilderInterface $builder [description]
     * @param array                $options [description]
     *
     * @return [type] [description]
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required' => true,
                'label' => 'Nom',
            ));

        $builder->add('language', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
            'label' => 'Langue',
            'choices' => $this->languagesToOptions($options['language']),
            'required' => true,
            'placeholder' => false,
            'expanded' => false,
            'multiple' => false,
            'choices_as_values' => true,
        ));

        $builder->add('skin', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'choices' => $options['skins'],
                'required' => true,
                'label' => 'Theme',
                'choices_as_values' => true,
            ));

        $builder->add('baseline', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required' => true,
                'label' => 'Baseline',
            ));

        $builder->add('meta_title', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required' => true,
                'label' => 'Meta Title',
            ));

        $builder->add('meta_description', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required' => true,
                'label' => 'Meta Description',
            ));

        $builder->add('meta_keywords', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required' => true,
                'label' => 'Meta Keywords',
            ));

        $builder->add('logo', 'Symfony\Component\Form\Extension\Core\Type\FileType', array(
                'required' => false,
                'label' => 'Logo',
            ));

        $builder->add('logoAlt', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required' => false,
                'label' => 'Texte alternatif du logo',
            ));

        $builder->add('logoUrl', 'Symfony\Component\Form\Extension\Core\Type\UrlType', array(
                'required' => false,
                'label' => 'Url du logo',
            ));

        $builder->add('logoTitle', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required' => false,
                'label' => 'Titre du lien sur le logo',
            ));

        $builder->add('background', 'Symfony\Component\Form\Extension\Core\Type\FileType', array(
                'required' => false,
                'label' => 'Background',
            ));

        $builder->add('submit', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
                'label' => 'Valider',
            ));
    }

    /**
     * {@inheritdoc}
     *
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
}
