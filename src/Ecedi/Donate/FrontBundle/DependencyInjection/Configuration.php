<?php

namespace Ecedi\Donate\FrontBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        $treeBuilder->root('donate_front')
        ->children()
            ->scalarNode('campaign')->defaultValue('campaign')->end()
            ->scalarNode('google_analytics')->defaultNull()->end()
            ->arrayNode('i18n')->prototype('scalar')->defaultNull()->end()->end()
            ->arrayNode('form')
                    ->children()
                        ->arrayNode('civility')->isRequired()
                        ->requiresAtLeastOneElement()
                        ->useAttributeAsKey('name')
                        ->prototype('scalar')->end()
                    ->end()
        ;

        return $treeBuilder;
    }
}
