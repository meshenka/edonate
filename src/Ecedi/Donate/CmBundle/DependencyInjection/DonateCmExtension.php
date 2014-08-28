<?php

namespace Ecedi\Donate\CmBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class DonateCmExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if($config['api_key'] !== false) {
            $container->setParameter('donate_cm.api_key',$config['api_key']);
            $container->setParameter('donate_cm.list_id',$config['list_id']);
            $container->setParameter('donate_cm.lot_import_nb_max',$config['lot_import_nb_max']);
            $container->setParameter('donate_cm.custom_fields',$config['custom_fields']);
        }
    }
}
