<?php
/**
 * @author Sylvain Gogel <sgogel@ecedi.fr>
 * @copyright Agence Ecedi (c) 2014
 */
namespace Ecedi\Donate\OgoneBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class DonateOgoneExtension extends Extension
{
    /**
     * {@inheritdoc}
     *
     * @since  2.2.0 async_postsale has been removed
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('donate_ogone.prod', $config['prod']);
        $container->setParameter('donate_ogone.pspid', $config['pspid']);
        $container->setParameter('donate_ogone.prefix', $config['prefix']);

        $container->setParameter('donate_ogone.security.sha1_in', $config['security']['sha1_in']);
        $container->setParameter('donate_ogone.security.sha1_out', $config['security']['sha1_out']);
    }
}
