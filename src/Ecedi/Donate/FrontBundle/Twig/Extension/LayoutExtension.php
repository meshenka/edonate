<?php

namespace Ecedi\Donate\FrontBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\Container;
use Ecedi\Donate\CoreBundle\Layout\LayoutManager;
use Symfony\Component\DependencyInjection\Exception\InactiveScopeException;
/**
 * Cette extension très simple exporte en global la configuration i18n du front.
 */
class LayoutExtension extends \Twig_Extension
{
    private $layoutManager;
    private $container;

    public function __construct(LayoutManager $layoutManager, Container $container)
    {
        $this->layoutManager = $layoutManager;
        $this->container = $container;
    }

    /**
     * Returns a list of global variables to add to the existing list.
     *
     * @return array An array of global variables
     */
    public function getGlobals()
    {   try{
            $request = $this->container->get('request');

            return ['layout' => $this->layoutManager->getDefault($request->getLocale())];
        } catch (InactiveScopeException $e) {
            //do nothing
            return [];
        }
    }


    public function getName()
    {
        return 'donate_front.layout.twig';
    }
}
