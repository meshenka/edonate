<?php

namespace Ecedi\Donate\CoreBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class DonateCoreExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('donate_core.equivalence', $config['equivalence']);
        $container->setParameter('donate_core.mail.donator', $config['mail']['donator']);
        $container->setParameter('donate_core.mail.webmaster', $config['mail']['webmaster']);
        $container->setParameter('donate_core.mail.noreply', $config['mail']['noreply']);
        $container->setParameter('donate_core.payment_methods', $config['payment_methods']);
    }
}
