<?php

namespace Ecedi\Donate\CoreBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Ecedi\Donate\CoreBundle\PaymentMethod\Plugin\PaymentMethodInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('donate_core');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        $rootNode
            ->children()
                ->arrayNode('payment_methods')
                    ->prototype('scalar')->defaultNull()->end()
                ->end()
                ->arrayNode('mail')
                    ->children()
                        ->booleanNode('donator')->defaultFalse()->end()
                        ->scalarNode('noreply')->defaultValue('noreply@ecedi.fr')->end()
                        ->arrayNode('webmaster')->prototype('scalar')->defaultNull()->end()->end()
                    ->end()
                ->end()
                ->arrayNode('equivalence')
                    ->children()
                        ->arrayNode(PaymentMethodInterface::TUNNEL_SPOT)
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('amount')->isRequired()->end()
                                    ->scalarNode('label')->isRequired()->end()
                                    ->scalarNode('currency')->defaultValue('EUR')->end()
                                    ->scalarNode('default')->defaultValue(false)->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode(PaymentMethodInterface::TUNNEL_RECURING)
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('amount')->isRequired()->end()
                                    ->scalarNode('label')->isRequired()->end()
                                    ->scalarNode('currency')->defaultValue('EUR')->end()
                                    ->scalarNode('default')->defaultValue(false)->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
        ;

        return $treeBuilder;
    }
}
