<?php

namespace Ecedi\Donate\OgoneBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('donate_ogone');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        $rootNode
            ->children()
                ->scalarNode('pspid')->isRequired()->end()
                ->booleanNode('prod')->defaultFalse()->end()
                ->booleanNode('async_postsale')->defaultFalse()->end()
                ->scalarNode('currency')->defaultValue('EUR')->end()
                ->scalarNode('prefix')->defaultValue('DEV')->end()
                ->arrayNode('security')->children()
                    ->scalarNode('sha1_in')->isRequired()->end()
                    ->scalarNode('sha1_out')->isRequired()->end()
                //->arrayNode('options')
;

        return $treeBuilder;
    }
}
