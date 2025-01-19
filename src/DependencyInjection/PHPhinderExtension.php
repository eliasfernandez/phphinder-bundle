<?php

namespace PHPhinderBundle\DependencyInjection;


use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class PHPhinderExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('phphinder.storage', $config['storage']);
        $container->setParameter('phphinder.name', $config['name']);
        $container->setParameter('phphinder.auto_sync', $config['auto_sync']);
        $container->setParameter('phphinder.sync_in_background', $config['sync_in_background']);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('phphinder.yaml');
    }

    public function getAlias(): string
    {
        return 'phphinder';
    }
}
