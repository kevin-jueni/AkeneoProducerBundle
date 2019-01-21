<?php

namespace Sylake\AkeneoProducerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sylake_akeneo_producer');

        $rootNode
            ->children()
            ->arrayNode('locales')
            ->prototype('scalar')->end()
            ->performNoDeepMerging()
            ->defaultValue(['de_CH', 'en_US'])
            ->end()
            ->scalarNode('channel')->end()
            ->scalarNode('category')->end()
            ->end();

        return $treeBuilder;
    }
}
