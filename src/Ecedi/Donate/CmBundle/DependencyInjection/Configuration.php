<?php
/**
 * @author Benoit Dautun <bdautun@ecedi.fr>
 * @copyright Agence Ecedi (c) 2015
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ecedi\Donate\CmBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

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
        $rootNode = $treeBuilder->root('donate_cm');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        $rootNode
            ->children()
                ->scalarNode('api_key')->defaultFalse()->end()
                ->scalarNode('list_id')->defaultFalse()->end()
                ->integerNode('lot_import_nb_max')->min(0)->max(200)->defaultValue(100)->end()
                ->arrayNode('custom_fields')
                    ->useAttributeAsKey('customer_getter')
                    ->prototype('array')
                    ->children()
                        ->scalarNode('cm_custom_field_name')->isRequired()->end()
                        ->arrayNode('options')
                            ->useAttributeAsKey('customer_option_value')
                            ->prototype('scalar')->isRequired()->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
