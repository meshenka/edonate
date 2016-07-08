<?php

namespace Ecedi\Donate\FrontBundle\Twig;

use Symfony\Component\DependencyInjection\Container;

/**
 * Cette extension très simple exporte en global la configuration i18n du front.
 */
class I18nTwigExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Returns a list of global variables to add to the existing list.
     *
     * @return array An array of global variables
     */
    public function getGlobals()
    {
        return array('i18n' => $this->container->getParameter('donate_front.i18n'));
    }

    public function getName()
    {
        return 'donate_front.i18n.twig';
    }
}
