<?php
/**
 * @author Sylvain Gogel <sgogel@ecedi.fr>
 * @copyright Agence Ecedi (c) 2015
 * @package eDonate
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace Ecedi\Donate\AdminBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class DonateAdminExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        // $loader->load('services.xml');
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('donate_admin.analytics.client_id', $config['analytics']['clientId']);
        $container->setParameter('donate_admin.analytics.data_ids', $config['analytics']['dataIds']);
        $container->setParameter('donate_admin.analytics.api_key', $config['analytics']['apiKey']);
    }
}
