<?php

namespace PHPhinderBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('phphinder');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('storage')->defaultValue('json')->end()
                ->scalarNode('name')->defaultNull('var')->end()
                ->booleanNode('auto_sync')->defaultTrue()->end()
                ->booleanNode('sync_in_background')->defaultFalse()->end()
            ->end()
        ;

        return $treeBuilder;
    }

}