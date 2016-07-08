<?php

namespace Ecedi\Donate\FrontBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class DonateFrontExtension extends Extension
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

        $container->setParameter('donate_front.form.civility', $config['form']['civility']);
        $container->setParameter('donate_front.google_analytics', $config['google_analytics']);
        $container->setParameter('donate_front.google_analytics.prefix', $config['google_analytics_prefix']);
        $container->setParameter('donate_front.i18n', $config['i18n']);
        $container->setParameter('donate_front.campaign', $config['campaign']);
    }
}
