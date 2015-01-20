<?php

namespace Ecedi\Donate\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Ecedi\Donate\CoreBundle\Entity\Layout;
use Symfony\Component\Intl\Intl;
/**
 * Une classe pour le formulaire des comptes utilisateurs
 */
class LayoutType extends AbstractType
{
    private $configuredLocales;

    public function __construct(array $configuredLocales)
    {
        $this->configuredLocales = $configuredLocales;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array(
                'required'  => true,
                'label'     => 'Nom',
            ));

        $builder->add('language', 'choice', array(
            'label'     => 'Langue',
            'choices' => $options['language'],
            'required' => true,
            //'preferred_choices' => array('fr'),
            'empty_value' => false,
            'expanded' => false,
            'multiple' => false,
        ));

        $builder->add('skin', 'choice', array(
                'choices'           => $options['skins'],
                'required'          => true,
                'label'             => 'Theme',
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

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'forms',
            'data_class' => 'Ecedi\Donate\CoreBundle\Entity\Layout',
            'language' => $this->getConfiguredLocales(),
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

    protected function getConfiguredLocales()
    {
        //$i18n = $this->container->getParameter('donate_front.i18n');
        $locales = [];

        foreach ($this->configuredLocales as $locale) {
            $locales[$locale] = Intl::getLocaleBundle()->getLocaleName($locale);
        }

        return $locales;
    }
}
